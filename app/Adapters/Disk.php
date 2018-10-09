<?php

namespace App\Adapters;

use App\Di;

class Disk implements IAdapter
{

    protected $disk;

    protected $diskDev;

    protected $src;

    protected $mount;

    protected $to;

    protected $data;

    protected $log;

    protected $isSimulation;

    protected $console;

    public function __construct(array $config)
    {
        $this->console = Di::get('console');

        foreach(['disk', 'src', 'mount', 'data', 'isSimulation'] as $key){
            $this->$key = $config[$key];
        }

        $this->to = $this->mount . $config['to'];
        $this->log = $this->to . $config['log'];
    }

    protected function mount()
    {
        //already mounted good
        if(exec("df | grep '^{$this->diskDev}.*{$this->mount}'")) 
        {
            $this->console->msg("Mounting {$this->disk} -> {$this->mount} OK");

            return;
        }

        //not mounted or bad mounted => mount good
        if($result = exec("df | grep '^{$this->diskDev}'"))
        {
            // $this->console->msg([
            //   "Fixing mount point from:", 
            //   $result, 
            //   "mount: {$this->disk} -> {$this->mount}"
            // ]);

            passthru("umount {$this->disk}", $error); $this->console->byeOnError($error);
        }

        passthru("mount {$this->disk} {$this->mount}", $error); $this->console->byeOnError($error);

        $this->console->msg("Mounting {$this->disk} -> {$this->mount}  OK");        
    }


    public function setup()
    {
        $this->diskDev = exec("readlink -f {$this->disk}");

        $this->mount();
    }

    public function runCommand($commands)
    {
        foreach($commands as $item)
        {
            $cmd = key($item);
            $output = $this->to . current($item);

            passthru("mkdir -p -m0777 " . dirname($output), $error); 
            $this->console->byeOnError($error);

            passthru("$cmd > $output", $error);
            $this->console->byeOnError($error);
        }
    }

    public function runRsync($data)
    {
        passthru("mkdir -p -m0777 " . dirname($this->log), $error); 
        $this->console->byeOnError($error);

        passthru("touch {$this->log}", $error);
        $this->console->byeOnError($error);

        $listOnly = $this->isSimulation ? '--list-only' : '';

        if(!$this->isSimulation){
            passthru("mkdir -p {$this->to}", $error);
            $this->console->byeOnError($error);
        }

        $rules = '';
        foreach(['include', 'exclude'] as $join){
            $unionKey = " --$join='";
            $rules .= $unionKey . implode("'". $unionKey, $data[$join]) . "'";
        }

        passthru("rsync \
            -rptgoDvl \
            --progress \
            --delete --force \
            --log-file={$this->log} \
            --no-inc-recursive \
            $rules \
            $listOnly \
            {$this->src}/ \
            {$this->to}/
            ", $error);
        $this->console->byeOnError($error);
    }


    public function run()
    {
        foreach($this->data as $data)
        {
            switch($data['type']){
                case 'command':
                    if($this->isSimulation) continue;
                    $this->runCommand($data['content']);
                    break;

                case 'rsync':
                    $this->runRsync($data['content']);
                    break;
            }
        }
    }

    public function shutdown()
    {
        passthru("umount {$this->disk}", $error); 
        $this->console->byeOnError($error);

        $this->console->msg("Un-mounting {$this->disk} OK");
    }

}
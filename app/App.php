<?php

namespace App;

use App\Di;

class App
{
    const ARG_NAME = 'name';
    const ARG_SIMULATION = 'simulate';
    const ARG_DEST = 'dest';

    /**
     * @var array
     */    
    protected $config;

    /**
     * @var App\Console
     */
    protected $console;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $isSimulation;

    /**
     * @var array
     */
    protected $dest;


    protected $adapters = [];


    public function __construct(array $config)
    {
        $this->config = $config;

        $this->console = Di::get('console');
    }


    public function run()
    {
        $this->console->msg('Backup mirror start');

        $this->checkRootUser();

        $this->setupName();

        $this->setupSimulation();
        
        $this->setupDest();

        $this->setupAdapters();

        $this->runAdapters();

        $this->shutdownAdapters();

        $this->console->msg('Backup mirror success');
    }


    protected function checkRootUser()
    {
        if(exec('id -un') !== 'root') 
            $this->console->error("use sudo or root user");
    }

    protected function setupName()
    {
        $name = $this->console->getOption(self::ARG_NAME, '');

        if(is_array($name)) $this->error("only one --name is supported");

        if(!in_array($name, array_keys($this->config)))
        {
            $validNames = implode(',', array_keys($this->config));

            $this->console->error(
                "--name: '$name' is not supported. Valid --name: '$validNames'");
        };
   
        $this->name = $name;
    }

    protected function setupSimulation()
    {
        $this->isSimulation = $this->console->hasOption(self::ARG_SIMULATION);
    }

    protected function setupDest()
    {
        $dest = $this->console->getOption(self::ARG_DEST, []);

        $dest = is_string($dest) ? explode(',', $dest) : $dest;

        $destNames = array_keys($this->config[$this->name]['dest']);

        if($diff = array_diff($dest, $destNames))
        {
            $destStr = implode(',', $destNames);

            $this->console->error(
                "--dest: '$diff[0]' is not supported. Valid --dest: '$destStr'");
        };
   
        $this->dest = $dest;
    }

    protected function setupAdapters()
    {
        foreach($this->dest as $dest)
        {
            $configDest = $this->config[$this->name]['dest'][$dest];

            $adapterName = 'adapter.' . $configDest['adapter'];

            $adapterConfig = array_merge(
                ['isSimulation' => $this->isSimulation],
                $configDest,
                array_diff_key($this->config[$this->name], ['dest' => 1])
            );

            $this->adapters[] = $adapter = Di::get($adapterName)($adapterConfig);

            $adapter->setup();
        }
    }

    protected function runAdapters()
    {
        foreach($this->adapters as $adapter)
        {
            $adapter->run();
        }        
    }

    protected function shutdownAdapters()
    {
        foreach($this->adapters as $adapter)
        {
            $adapter->shutdown();
        }        
    }

}
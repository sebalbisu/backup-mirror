<?php

namespace App;

class Console
{
    function getColor($name)
    {
        $black = "\33[0;30m";
        $darkgray = "\33[1;30m";
        $blue = "\33[0;34m";
        $lightblue = "\33[1;34m";
        $green = "\33[0;32m";
        $lightgreen = "\33[1;32m";
        $cyan = "\33[0;36m";
        $lightcyan = "\33[1;36m";
        $red = "\33[0;31m";
        $lightred = "\33[1;31m";
        $purple = "\33[0;35m";
        $lightpurple = "\33[1;35m";
        $brown = "\33[0;\33m";
        $yellow = "\33[1;33m";
        $lightgray = "\33[0;37m";
        $white = "\33[1;37m";
        
        return $$name;
    }

    function popup($msg)
    {
        $this->msg($msg);

        exec("notify-send '$msg'");
    }


    function msg($text, $color = 'yellow')
    {
        if($color) {$color = $this->getColor($color);}

        if(is_array($text))
        {
            $text = implode(PHP_EOL, $text);
        }

        echo $color . $text . $this->getColor('white') . PHP_EOL;
    }


    function ask($question, $default = '')
    {
        $this->msg ($question, 'lightpurple');

        $response = rtrim(fgets(STDIN));

        return $response === '' ? $default : $response;
    }


    function output($text)
    {
        $this->msg($text, 'white');
    }


    function error($text = null)
    {
        if($text) $this->msg('ERROR: ' . $text, 'lightred');

        $this->popup('ERROR in backup');

        exit(1);
    }


    function bye($text = null)
    {
        if($text) $this->msg($text);

        exit(0);
    }


    function byeOnError($e = 0, $msg = null)
    {
        if($e) $this->error($msg);
    }

    /**
     * @param  string $name    
     * @param  mixed $default
     * @return array|scalar
     */
    function getOption($name, $default = null)
    {
        $options = getopt('', ["$name:"]);

        return  $options ? $options[$name] : $default;
    }


    function hasOption($name)
    {
        return !empty(getopt('', ["$name"]));
    }
}
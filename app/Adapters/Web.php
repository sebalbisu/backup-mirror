<?php

namespace App\Adapters;

class Web implements IAdapter
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function setup()
    {

    }

    public function run()
    {

    }

    public function shutdown()
    {

    }    
}
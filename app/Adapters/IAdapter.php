<?php

namespace App\Adapters;


interface IAdapter 
{
    public function setup();

    public function run();

    public function shutdown();
}
<?php

namespace App;

class Di
{
    static protected $map;

    static public function get($name)
    {
        $mapped = isset(static::$map[$name]) ? static::$map[$name] : $name;

        if(!is_string($mapped)) return $mapped;

        return function(...$params) use($mapped)
        {
            return new $mapped(...$params);
        };
    }

    static public function config($name, $value)
    {
        static::$map[$name] = $value;
    }
}
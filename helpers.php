<?php

if (!function_exists('config'))  
{
    function config($key) {
        $conf = \Stoykov\Konf\Repository::getInstance();

        return $conf->get($key);
    }
}
<?php

namespace AppBundle\Configuration;

class Json
{
    public static function load($file)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file . '.json');
    }
}
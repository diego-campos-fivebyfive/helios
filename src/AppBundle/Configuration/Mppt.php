<?php

namespace AppBundle\Configuration;

abstract class Mppt
{
    public static function combinations($number = null)
    {
        $combinations =  json_decode(self::json(), true);

        if($number){
            if(array_key_exists($number, $combinations)){
                return $combinations[$number];
            }
            throw new \InvalidArgumentException('Mppt number is not registered');
        }

        return $combinations;
    }

    public static function json()
    {
        return file_get_contents(__DIR__ . '/mppt_combinations.json');
    }
}
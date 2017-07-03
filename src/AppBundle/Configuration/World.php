<?php

namespace AppBundle\Configuration;

class World
{
    public static function countries($format = 'array')
    {
        $countries = Json::load('world');

        if('array' == $format){
            $countries = json_decode($countries, true);
        }

        return $countries;
    }
}
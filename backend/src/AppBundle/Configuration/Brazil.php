<?php

namespace AppBundle\Configuration;

class Brazil
{
    public static function regions()
    {
        return [
            'north' => ['AC', 'AM', 'AP', 'PA', 'RO', 'RR', 'TO'],
            'northeast' => ['AL', 'BA', 'CE', 'MA', 'PB', 'PE', 'PI', 'SE', 'RN'],
            'midwest' => ['GO', 'MS', 'MT'],
            'southeast' => ['ES', 'MG', 'RJ', 'SP'],
            'south' => ['PR', 'RS', 'SC']
        ];
    }

    public static function states()
    {
        $states = [];
        foreach(self::all() as $initials => $state) {
            $states[$initials] = $state->name;
        }

        return $states;
    }

    public static function cities($state)
    {
        $state = self::state($state);
        if($state)
            return array_combine($state->cities, $state->cities);

        return null;
    }

    /**
     * @param $id
     * @return null | object
     */
    public static function state($id)
    {
        $states = self::all();
        foreach($states as $state){
            if($id == $state->initials || $id == $state->name || $state->initials == strtoupper($id)){
                return $state;
            }
        }

        return null;
    }

    public static function all()
    {
        return json_decode(self::json());
    }

    public static function json()
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'brazil.json');
    }
}
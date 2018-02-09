<?php

namespace App\Sices\Nfe;

class Parser
{
    private static $mapping = [
        'uf' => 2,
        'year_month' => 4, //TODO
        'cnpj' => 14,
        'model' => 2,
        'serial' => 3,
        'invoice' => 9,
        'format' => 1,
        'code' => 8,
        'dv' => 1,
        'reference' => 9,
        'billed_at' => 8,
        'billing' => 1
    ];

    /**
     * @param string $base
     * @return array
     */
    public static function extract(string $base)
    {
        $data = [];
        $offset = 0;
        foreach (self::$mapping as $key => $size){
            $data[$key] = substr($base, $offset, $size);
            $offset += $size;
        }

        return $data;
    }
}

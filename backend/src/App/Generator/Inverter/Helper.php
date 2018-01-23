<?php

namespace App\Generator\Inverter;

/**
 * Class Helper
 */
class Helper
{
    /**
     * @param array $inverters
     * @param $desired
     * @param $fdi
     * @return float|int
     */
    public static function adjustPower(array $inverters, $desired, $fdi)
    {
        $newPower = $desired;
        foreach ($inverters as $inverter){
            $reference = $inverter["nominal_power"] / $fdi;
            if ($desired < $reference) {
                $newPower = $reference;
            }
        }

        return $newPower;
    }

    /**
     * @param array $inverters
     * @return array
     */
    public static function mpptOperations(array $inverters)
    {
        $mppts = array();

        for ($i=0; $i<count($inverters); $i++){
            $parallel = $inverters[$i]["mppt_parallel"];
             $number = $inverters[$i]["mppt_number"];

            if ($parallel == 1){
                $mppts[$i][0] =  $number;
            }else{
                for ($m=0; $m < $number; $m++){
                    $mppts[$i][$m] = 1;
                }
            }

        }

        return $mppts;
    }
}

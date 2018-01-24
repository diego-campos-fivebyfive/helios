<?php

namespace App\Generator\Inverter;

use App\Generator\Common\Math;

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

    /**
     * @param $numberOfElements
     * @param $maxOfElements
     * @return array
     */
    public static function allCombinations($numberOfElements, $maxOfElements)
    {
        $possibilities = Math::combinations($numberOfElements, $maxOfElements);
        $combination = [];

        $counter = array_fill(0, $maxOfElements, 0);

        for ($i = 0; $i < $possibilities; $i++) {
            $combination[$i] = $counter;

            $counter[$maxOfElements - 1] += 1;

            for ($k = 1; $k < $maxOfElements; $k++) {
                if ($counter[$maxOfElements - $k] >= $numberOfElements) {
                    $counter[$maxOfElements - ($k + 1)] += 1;
                    for ($j = $k; $j >= 1; $j--) {
                        $counter[$maxOfElements - $j] = $counter[$maxOfElements - ($j + 1)];
                    }
                }
            }
        }

        return $combination;
    }

    /**
     * @param $numberOfElements
     * @param $maxOfElements
     * @return array
     */
    public static function allCombinationsOptimized($numberOfElements, $maxOfElements)
    {
        $possibilities = Math::combinations($numberOfElements, $maxOfElements);
        $combination = array();

        for ($i = 0; $i < $numberOfElements; $i++) {
            $counter = array_fill(0, $maxOfElements, $i);
            $combination[$i] = $counter;
        }

        $counter = array_fill(0, $maxOfElements, 0);

        for ($i = $numberOfElements; $i < $possibilities; $i++) {

            $counter[$maxOfElements - 1] += 1;

            for ($k = 1; $k < $maxOfElements; $k++) {
                if ($counter[$maxOfElements - $k] >= $numberOfElements) {
                    $counter[$maxOfElements - ($k + 1)] += 1;
                    for ($j = $k; $j >= 1; $j--) {
                        $counter[$maxOfElements - $j] = $counter[$maxOfElements - ($j + 1)];
                    }
                }
            }

            $unique = array_unique($counter);
            if (count($unique) == 1) {
                $counter[$maxOfElements - 1] += 1;
            }

            $combination[$i] = $counter;
        }

        return $combination;
    }
}

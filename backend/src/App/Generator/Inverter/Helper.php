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
}

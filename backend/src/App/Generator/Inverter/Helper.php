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
        $mppts = [];

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
        $combination = [];

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

    /**
     * @param array $data
     * @param array $alternatives
     * @return array
     */
    public static function filterActives(array $data, array $alternatives = []){

        foreach ($data as $key => $item){
            if(null != $item = self::filterAlternativeItem($item, $alternatives)){
                $data[$key] = $item;
            }else{
                unset($data[$key]);
            }
        }

        uasort($data, function ($a, $b){
            return $a["nominal_power"] > $b["nominal_power"];
        });

        return array_values($data);
    }

    /**
     * @param $inverters
     * @param $phaseVoltage
     * @param $phaseNumber
     * @return array
     */
    public static function filterPhases($inverters, $phaseVoltage, $phaseNumber)
    {
        $net = [$phaseVoltage, $phaseNumber];

        if ($net == [220, 1] or $net == [220, 2]) {
            $count = count($inverters);
            for ($i = 0; $i < $count; $i++) {
                if ($inverters[$i]["phase_number"] > $phaseNumber) {
                    unset($inverters[$i]);
                }
            }

            $inverters = array_values($inverters);
        }

        if ($net == [380, 3] or $net == [220, 3]) {
            $count = count($inverters);
            for ($i = 0; $i < $count; $i++) {
                $inverterPhaseVoltage = $inverters[$i]["phase_voltage"];
                $inverterCompatibility = $inverters[$i]["compatibility"];

                if ($inverterPhaseVoltage == $phaseVoltage)
                    $inverterCompatibility = 0;
                if ($inverterPhaseVoltage > 380 or $inverterCompatibility > 0)
                    unset($inverters[$i]);
            }

            $inverters = array_values($inverters);
        }

        if ($phaseVoltage > 380) {
            $count = count($inverters);
            for ($i = 0; $i < $count; $i++)
                if ($inverters[$i]["phase_voltage"] != $phaseVoltage)
                    unset($inverters[$i]);
            $inverters = array_values($inverters);
        }

        return $inverters;
    }

    public static function filterPower($inverters, $desiredPower)
    {
        $count = count($inverters);
        for ($i = 0; $i < $count; $i++) {
            $maxShow = $inverters[$i]["pow_max_show"];
            $minShow = $inverters[$i]["pow_min_show"];
            if ($maxShow > 0) {
                if ($desiredPower < $minShow or $desiredPower > $maxShow) {
                    unset($inverters[$i]);
                }
            }
        }
        $inverters = array_values($inverters);

        if ($desiredPower >= 75) {
            $count = count($inverters);
            for ($i = 0; $i < $count; $i++) {
                if ($inverters[$i]["phase_number"] < 3) {
                    unset($inverters[$i]);
                }
            }

            $inverters = array_values($inverters);
        }

        if ($desiredPower >= 500) {
            $count = count($inverters);
            for ($i = 0; $i < $count - 1; $i++) {
                unset($inverters[$i]);
            }
            $inverters = array_values($inverters);
        }

        return $inverters;
    }

    public static function inverterChoices($inverters, $desiredPower, $fdiMin, $fdiMax)
    {
        $inferiorLimit = $desiredPower * $fdiMin;
        $upperLimit = $desiredPower * $fdiMax;

        $selected = [];

        for ($i = 0; $i <= 30; $i++) {
            $combinations = self::allCombinationsOptimized(count($inverters), $i + 1);
            for ($k = 0; $k < count($combinations); $k++) {
                $accumulatedPower = 0;

                for ($j = 0; $j < count($combinations[$k]); $j++) {
                    $index = $combinations[$k][$j];
                    $accumulatedPower += $inverters[$index]["nominal_power"];

                    $selected[$j] = $inverters[$index];
                }

                if ($accumulatedPower >= $inferiorLimit and $accumulatedPower <= $upperLimit)
                    break 2;
            }
            $selected = [];
        }

        return $selected;
    }

    /**
     * @param $item
     * @param array $alternatives
     * @return null
     */
    private static function filterAlternativeItem($item, array &$alternatives = []){

        if(!is_null($item)){
            if($item['active']){
                return $item;
            }elseif(array_key_exists('alternative', $item)){

                $key = array_search($item['alternative'], array_column($alternatives, 'id'));

                if(is_numeric($key)) {

                    $next = &$alternatives[$key];

                    if (!array_key_exists('visited', $next) || !$next['visited']) {
                        $next['visited'] = true;
                        return self::filterAlternativeItem($next, $alternatives);
                    }
                }
            }
        }

        return null;
    }
}

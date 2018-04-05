<?php

namespace App\Generator\StringBox;

use App\Generator\Inverter\Helper as InverterHelper;

class Helper
{
    /**
     * @param $arrangementChoice
     * @return array
     */
    public static function getParameters($arrangementChoice)
    {

        $inputs = 0;
        for ($i = 0; $i < count($arrangementChoice); $i++) {
            $inputs += $arrangementChoice[$i]["par"];
        }

        $outputs = count($arrangementChoice);

        $parameters = [
            "inputs" => $inputs,
            "outputs" => $outputs
        ];

        return $parameters;
    }

    /**
     * @param $stringboxParameters
     * @param $allStringbox
     * @return array
     */
    public static function getChoices($stringboxParameters, $allStringbox)
    {
        $inputs = $stringboxParameters["inputs"];
        $outputs = $stringboxParameters["outputs"];

        $selection = array();

        for ($i = 0; $i <= 10; $i++) {
            $combinations = InverterHelper::allCombinations(count($allStringbox), $i + 1);
            $nCombinations = count($combinations);

            for ($k = 0; $k < $nCombinations; $k++) {
                $acuIn = 0;
                $acuOut = 0;
                $nElements = count($combinations[$k]);

                for ($j = 0; $j < $nElements; $j++) {
                    $index = $combinations[$k][$j];
                    $acuIn += $allStringbox[$index]["inputs"];
                    $acuOut += $allStringbox[$index]["outputs"];

                    $selection[$j] = $allStringbox[$index];
                }

                $testIn = $inputs - $acuIn;
                $tesOut = $outputs - $acuOut;

                if (($testIn <= 0) and ($tesOut <= 0)) {
                    break 2;
                }
            }
            $selection = array();
        }
        return $selection;
    }

    /**
     * @param array $inverters
     * @param array $stringBoxes
     * @return array
     */
    public static function configure(array $inverters, array $stringBoxes)
    {
        $inProtection = InverterHelper::hasProtection($inverters);

        for($i = 0; $i < count($inverters); $i++) {
            if ($inProtection[$i] == 0 || $inProtection[$i] == null) {
                $stringBoxParameters = self::getParameters($inverters[$i]['arrangements']);
                $stringBoxesChoices = self::getChoices($stringBoxParameters, $stringBoxes);
                $inverters[$i]['string_boxes'] = $stringBoxesChoices;
            }
        }

        return $inverters;
    }
}

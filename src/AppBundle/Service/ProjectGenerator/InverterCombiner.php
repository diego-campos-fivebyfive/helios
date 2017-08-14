<?php

namespace AppBundle\Service\ProjectGenerator;

class InverterCombiner
{
    /**
     * @param array $inverters
     * @param $min
     * @return bool
     */
    public static function combine(array &$inverters, $min)
    {
        foreach ($inverters as $inverter){
            $inverter->quantity = 0;
        }

        $limit = AbstractConfig::$maxInverters;
        $comb = 2;
        $cont = [];
        for ($j = $comb; $j <= $limit; $j++) {

            $cont = array_fill(0, $j, 0);
            $cont2 = array_fill(0, $j, 0);
            $top = count($inverters) - 1;
            $totalPower = count($cont) * $inverters[0]->getNominalPower();

            if ($totalPower >= $min) {

                for ($i = 0; $i < self::calculateCombinations(count($inverters), $j); $i++) {

                    $result = 0;
                    for ($y = 0; $y < count($cont); $y++) {
                        $result += $inverters[$cont[$y]]->getNominalPower();
                    }

                    if ($result < $min) {
                        break 2;
                    }

                    $cont2 = $cont;

                    $cont[$j - 1] += 1;
                    for ($k = 1; $k < $j; $k++) {
                        if ($cont[$j - $k] > $top) {
                            $cont[$j - ($k + 1)] += 1;
                            for ($z = $k; $z >= 1; $z--) {
                                $cont[$j - $z] = $cont[$j - ($z + 1)];
                                if ($cont[0] > $top) {
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($cont) == $limit) {
            return false;
        }

        sort($cont2);

        foreach ($cont2 as $attachKey) {
            $inverters[$attachKey]->quantity += 1;
        }

        foreach ($inverters as $key => $inverter) {
            if (!$inverter->quantity) {
                unset($inverters[$key]);
            }
        }

        return true;
    }

    /**
     * @param $a
     * @param $b
     * @return float|int
     */
    private static function calculateCombinations($a, $b)
    {
        $number = $a + $b - 1;
        $cont = $b;
        for ($i = $number - 1; $cont > 1; $i--) {
            $number *= $i;
            $cont -= 1;
        }
        return $number / self::factorial($b);
    }

    /**
     * @param $num
     * @return int
     */
    private static function factorial($num)
    {
        $acu = $num;
        if ($num == 0) {
            return 1;
        } else {
            for ($i = $num - 1; $i != 0; $i--) {
                $acu *= $i;
            }
            return $acu;
        }
    }
}
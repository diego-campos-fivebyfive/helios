<?php

namespace App\Generator\Common;

/**
 * Class Math
 */
class Math
{
    /**
     * @param int $number
     * @return int
     */
    public static function factorial(int $number)
    {
        $factorial = $number;
        if (0 == $number) {
            return 1;
        } else {
            for ($i = $number-1; $i > 0; $i--) {
                $factorial *= $i;
            }
            return $factorial;
        }
    }

    /**
     * @param int $a
     * @param int $b
     * @return float|int
     */
    public static function combinations(int $a, int $b)
    {
        $numerator = $a + $b - 1;
        $cont = $b;

        for ($i = $numerator - 1; $cont > 1; $i--) {
            $numerator *= $i;
            $cont -= 1;
        }

        return $numerator / self::factorial($b);
    }
}

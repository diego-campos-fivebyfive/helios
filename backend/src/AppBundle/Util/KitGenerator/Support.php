<?php


namespace AppBundle\Util\KitGenerator;


class Support
{
    public static function factorial($number)
    {
        $factorial = $number;

        if ($number >= 1) {
            for ($i = $number - 1; $i != 0; $i--) {
                $factorial *= $i;
            }
        } else {
            return 1;
        }

        return $factorial;
    }

    public static function sign($number)
    {
        if ($number < 0) {
            return -1;
        } else {
            return 1;
        }
    }

    public static function combine($a, $b)
    {
        $num = $a + $b - 1; //2
        $cont = $b; //2
        for ($i = $num - 1; $cont > 1; $i--) {   //1
            $num *= $i;
            $cont -= 1;
        }
        return $num / self::factorial($b);
    }


}
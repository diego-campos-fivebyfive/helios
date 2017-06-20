<?php


namespace AppBundle\Util\KitGenerator;


class Support
{
public static function factorial($number){
    $factorial = $number;

    if($number >= 1) {
    for($i= $number-1; $i != 0; $i--){
        $factorial *= $i;
    }
    }
    else{
        return 1;
    }

    return $factorial;
}
}
<?php

/**
 * Class WorkingDays
 * @property $easter (Páscoa)
 * @property $carnival (Carnaval)
 * @property $ashes (Cinzas)
 * @property $goodFrida (Sexta-feira santa)
 * @property $corpusChristi (Corpus Christi)
 */
class WorkingDays{

    /**
     * @var array
     * Fixed holidays
     */
    private $holidays = [
        '01' => array(
            '01' => 'Ano novo'
        ),
        '04' => array(
            '21' => 'Dia de tiradentes'
        ),
        '05' => array(
            '01' => 'Dia do trabalho'
        ),
        '07' => array(
            '09' => 'Revolução Constitucionalista'
        ),
        '09' => array(
            '07' => 'Independência do Brasil'
        ),
        '10' => array(
            '12' => 'Nossa senhora Aparecida'
        ),
        '11' => array(
            '15' => 'Proclamação da República',
            '02' => 'Finados',
            '20' => 'Consciência Negra'
        ),
        '12' => array(
            '25' => 'Natal'
        )
    ];

    /**
     * @var array
     * Comment or uncomment to release/block days of the week
     */
    private $weekDays = [
        //0,  // Sunday
        1,  // Monday
        2,  // Tuesday
        3,  // Wednesday
        4,  // Thursday
        5,  // Friday
        //6,  // Saturday
    ];

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * WorkingDays constructor.
     * @param DateTime|null $date
     */
    private function __construct(\DateTime $date = null)
    {
        $this->date = $date ? $date : new \DateTime();
    }

    /**
     * @param DateTime|null $date
     */
    public static function create(\DateTime $date = null)
    {
        return new self($date);
    }

    /**
     * @param int $days
     * @return DateTime
     */
    public function next($days)
    {
        $this->date->setTime(0, 0, 0);
        $year = $this->date->format('Y');

        $x = 24;
        $y = 5;
        $a = $year % 19;
        $b = $year % 4;
        $c = $year % 7;
        $d = ((19 * $a) + $x) % 30;
        $e = ((2 * $b) + (4 * $c) + (6 * $d) + $y) % 7;
        if (($d + $e) < 10){
                $easterMonth = "03";
            $easterDay = $d + $e + 22;
            if ($easterDay < 10){
                $easterDay = "0".$easterDay;
            }
        }else{
            $easterMonth = "04";
            $easterDay = $d + $e - 9;
            if ($easterDay < 10){
                $easterDay = "0".$easterDay;
            }
        }

        if ($year == 2049){
            $easterMonth = "04";
            $easterDay = "18";
        }

        if ($year == 2076){
            $easterMonth = "04";
            $easterDay = "19";
        }

        $easterTime = strtotime(date("$easterDay-$easterMonth-$year"));
        $carnivalTime = strtotime("- 47 day", $easterTime);
        $carnivalTime1 = strtotime("- 48 day", $easterTime);
        $ashes_time = strtotime("- 46 day", $easterTime);
        $corpus_time = strtotime("+ 60 day", $easterTime);
        $goodFriday_time = strtotime("- 2 day", $easterTime);
        $carnivalMonth = date("m", $carnivalTime);
        $carnivalDay = date("d", $carnivalTime);
        $carnivalMonth1 = date("m", $carnivalTime1);
        $carnivalDay1 = date("d", $carnivalTime1);
        $ashesMonth = date("m", $ashes_time);
        $ashesDay = date("d", $ashes_time);
        $corpusChristiMonth = date("m", $corpus_time);
        $corpusChristiDay = date("d", $corpus_time);
        $goodFridayMonth = date("m", $goodFriday_time);
        $goodFridayDay = date("d", $goodFriday_time);

        $this->holidays[$easterMonth][$easterDay] = "Páscoa";
        $this->holidays[$carnivalMonth][$carnivalDay] = "Carnaval";
        $this->holidays[$carnivalMonth1][$carnivalDay1] = "Carnaval";
        $this->holidays[$ashesMonth][$ashesDay] = "Cinzas";
        $this->holidays[$corpusChristiMonth][$corpusChristiDay] = "Corpus Christi";
        $this->holidays[$goodFridayMonth][$goodFridayDay] = "Sexta feira santa";

        ksort($this->holidays);

        $currentDate = $this->date;
        while ($days > 0){

            $currentDate->add(new \DateInterval('P1D'));

            if($year != $currentDate->format('Y')){
                return self::create($currentDate)->next($days);
            }

            $expiring_mes = $currentDate->format('m');
            $expiring_dia = $currentDate->format('d');

            if (array_key_exists($expiring_mes, $this->holidays)){
                if (array_key_exists($expiring_dia, $this->holidays[$expiring_mes])){
                    continue;
                }
            }

            if (!in_array($currentDate->format('w'), $this->weekDays)){
                continue;
            }

            $days -= 1;
        }

        return $currentDate;
    }

    /**
     * @return array
     */
    public function holidays()
    {
        return $this->holidays;
    }
}

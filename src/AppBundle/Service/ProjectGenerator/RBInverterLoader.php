<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;

class RBInverterLoader
{
    /**
     * @var float
     */
    private $delay = .5;

    /**
     * @var InverterManager
     */
    private $manager;

    /**
     * @var string
     */
    private $fields = 'i';

    /**
     * @var \AppBundle\Entity\Component\MakerInterface|int
     */
    private $maker;

    private $power;

    /**
     * @var int
     */
    private $attempts;

    /**
     * @var \Doctrine\ORM\QueryBuilder $qb
     */
    private $qb;

    /**
     * @var float
     */
    private $fdiMin = 0.75;

    /**
     * @var float
     */
    private $fdiMax = 1;

    /**
     * @var bool
     */
    private $increasing = false;

    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @inheritDoc
     */
    public function __construct($host, $database, $user, $password)
    {
        \R::setup('mysql:host=' .$host. ';dbname='.$database.'', $user, $password);
    }

    /**
     * @param $power
     * @return InverterLoader
     */
    public function power($power)
    {
        //return $this->range(($this->fdiMin * $power), ($this->fdiMax * $power));
        $this->power = $power;

        return $this;
    }

    /**
     * @param $maker
     * @return $this
     */
    public function maker($maker)
    {
        /*if ($maker instanceof MakerInterface) {
            $maker = $maker->getId();
        }

        $this->maker = $maker;

        $this->qb->andWhere('i.maker = :maker');

        $this->qb->setParameter('maker', $maker);*/

        $this->maker = $maker;

        return $this;
    }

    /**
     * @param $min
     * @param $max
     * @return $this
     */
    public function range($min, $max)
    {
        if(!$this->increasing) {
            $this->qb->andWhere(
                $this->qb->expr()->between('i.nominalPower', ':min', ':max')
            );
        }

        $this->qb->setParameter('min', $min);
        $this->qb->setParameter('max', $max);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return InverterLoader
     */
    public function project(ProjectInterface $project)
    {
        $this->project = $project;

        return $this->power($project->getInfPower());
    }

    /**
     * @return array
     */
    public function get()
    {
        $min = $this->power * $this->fdiMin;
        $max = $this->power * $this->fdiMax;
        $attempts = 1;

        $data = $this->find($min, $max, $this->maker);

        if (!count($data)) {
            for ($i = 300; $i >= 0; $i -= 15) {

                $attempts++;

                $data = $this->find(($min * ($i / 1000)), $max, $this->maker);

                if (count($data) >= 2) {
                    break;
                }
            }
        }else{
            array_splice($data, 1);
        }

        while (!count($data)){
            $this->increasing = true;
            $this->power += 0.2;
            $min = $this->power * $this->fdiMin;
            $max = $this->power * $this->fdiMax;
            $data = $this->find($min, $max, $this->maker);
        }

        $quantity = $attempts && !$this->increasing ? 0 : 1;

        array_walk($data, function (&$bean) use($quantity) {
            $bean->quantity = $quantity;
        });

        if ($attempts > 1) {
            $data = array_values($data);
            $this->resolveCombinations($data, $min, $max);
        }

        $this->attempts = $attempts;

        $this->project->setInfPower($this->power);

        return array_values($data);
    }

    /**
     * @return int
     */
    public function attempts()
    {
        return $this->attempts;
    }

    /**
     * @param array $data
     * @return array
     */
    private function increase(array &$data)
    {
        $power = $this->power;
        while (!count($data)){

            //$power = $this->project->getInfPower() + 0.2;
            //$this->project->setInfPower($power);
            //$this->power($power);

            //$min = $power * $this->fdiMin;
            //$max = $power * $this->fdiMax;
            //$inverters = $this->find($min, $max, $this->maker);
            //$this->project->setInfPower($power);
            //$this->power($power);

            $power += 0.2;
            $this->project->setInfPower($power);

            $data = $this->project($this->project)->attempt();
        }

        dump($data); die;

        //$this->project->setInfPower($power);

        return $inverters;
    }

    /**
     * @param array $inverters
     * @param $min
     * @param $max
     * @return array
     */
    private function resolveCombinations(array &$inverters, $min, $max)
    {
        $comb = 2;
        $cont = [];
        for ($j = $comb; $j <= 10; $j++) {

            $cont = array_fill(0, $j, 0);
            $top = count($inverters) - 1;

            for ($i = 0; $i < self::calculateCombinations(count($inverters), $j); $i++) {

                $result = 0;
                for ($y = 0; $y < count($cont); $y++) {
                    $result += $inverters[$cont[$y]]->nominal_power;
                }

                if ($result <= $max and $result >= $min) {
                    break 2;
                }

                $cont[$j - 1] += 1;
                for ($k = 1; $k < $j; $k++) {
                    if ($cont[$j - $k] > $top) {
                        $cont[$j - ($k + 1)] += 1;
                        for ($z = $k; $z >= 1; $z--) {
                            $cont[$j - $z] = $cont[$j - ($z + 1)];
                            if($cont[0] > $top){
                                break 3;
                            }
                        }
                    }
                }
            }
        }

        if (count($cont) == 10) {
            return [];
        }

        rsort($cont);

        foreach ($cont as $attachKey) {
            $inverters[$attachKey]->quantity += 1;
        }

        foreach ($inverters as $key => $inverter) {
            if (!$inverter->quantity) {
                unset($inverters[$key]);
            }
        }

        $inverters = array_values($inverters);
    }

    /**
     * Initialize QueryBuilder
     */
    private function init()
    {
        $this->qb = $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($this->fields)
            ->from($this->manager->getClass(), 'i')
            ->orderBy('i.nominalPower', 'asc');
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

    private function find($min, $max, $maker){
        return \R::find('app_component_inverter', 'maker = ? and (nominal_power between ? and ?)', [$maker, $min, $max]);
    }
}
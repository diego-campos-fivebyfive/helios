<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Manager\InverterManager;

/**
 * Class InverterLoader
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class DoctrineInverterLoader
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
    public function __construct(InverterManager $manager)
    {
        $this->manager = $manager;

        $this->init();
    }

    /**
     * @param $power
     * @return InverterLoader
     */
    public function power($power)
    {
        return $this->range(($this->fdiMin * $power), ($this->fdiMax * $power));
    }

    /**
     * @param $maker
     * @return $this
     */
    public function maker($maker)
    {
        if ($maker instanceof MakerInterface) {
            $maker = $maker->getId();
        }

        $this->maker = $maker;

        $this->qb->andWhere('i.maker = :maker');

        $this->qb->setParameter('maker', $maker);

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
        $attempts = 1;
        $query = $this->qb->getQuery();
        $min = $query->getParameter('min')->getValue();
        $max = $query->getParameter('max')->getValue();

        $inverters = $query->getResult();

        if (!count($inverters)) {
            for ($i = 300; $i >= 0; $i -= 15) {

                $attempts++;

                $this->qb->setParameter('min', ($min * ($i / 1000)));

                $inverters = $this->qb->getQuery()->getResult();

                if (count($inverters) >= 2) {
                    break;
                }
            }
        }else{
            array_splice($inverters, 1);
        }

        $power = $this->project->getInfPower();
        while (!count($inverters)){

            $this->increasing = true;
            $power += 0.2;
            $min = $power * $this->fdiMin;
            $max = $power * $this->fdiMax;

            $this->qb->setParameters([
                'min' => $min,
                'max' => $max,
                'maker' => $this->maker
            ]);

            $inverters = $this->qb->getQuery()->getResult();
        }

        $this->project->setInfPower($power);

        $quantity = $attempts && !$this->increasing ? 0 : 1;

        array_walk($inverters, function (&$inverter) use($quantity) {
            $inverter->quantity = $quantity;
        });

        if ($attempts > 1 && !$this->increasing) {
            $this->resolveCombinations($inverters, $min, $max);
        }

        $this->attempts = $attempts;

        return $inverters;
    }

    /**
     * @return int
     */
    public function attempts()
    {
        return $this->attempts;
    }

    /**
     * @param array $inverters
     * @return array
     */
    private function increase(array $inverters)
    {
        while (!count($inverters)){
            $power = $this->project->getInfPower() + 0.2;
            $this->project->setInfPower($power);
            $this->power($power);

            $inverters = $this->project($this->project)->get();
        }

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
                    $result += $inverters[$cont[$y]]->getNominalPower();
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
}
<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Manager\InverterManager;

/**
 * Class InverterLoader
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class InverterLoader
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
        $this->qb->andWhere(
            $this->qb->expr()->between('i.nominalPower', ':min', ':max')
        );

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

        if (empty($inverters)) {
            for ($i = 0.3; $i > 0.025; $i -= 0.015) {

                sleep($this->delay);

                $attempts++;

                $this->qb->setParameter('min', ($min * $i));

                $inverters = $this->qb->getQuery()->getResult();

                if (count($inverters) >= 2) {
                    break;
                }
            }
        }

        array_walk($inverters, function (&$inverter) use($attempts) {
            //$inverter['quantity'] = $attempts > 1 ? 0 : 1 ;
        });

        if ($attempts > 1) {
            self::resolveCombinations($inverters, $min, $max);
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
     * @param $min
     * @param $max
     * @throws \Exception
     */
    public static function resolveCombinations(array &$inverters, $min, $max)
    {
        $comb = 2;

        $cont = [];
        for ($j = $comb; $j <= 50; $j++) {

            $cont = array_fill(0, $j, 0);
            $top = count($inverters) - 1;

            for ($i = 0; $i < self::calculateCombinations(count($inverters), $j); $i++) {

                $result = 0;
                for ($y = 0; $y < count($cont); $y++) {
                    $result += $inverters[$cont[$y]]["nominal_power"];
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
                        }
                    }
                }
            }
        }

        if (count($cont) == 50) {
            throw new \Exception('Exhausted combinations');
        }

        rsort($cont);

        foreach ($cont as $attachKey) {
            $inverters[$attachKey]['quantity'] += 1;
        }

        foreach ($inverters as $key => $inverter) {
            if (!$inverter['quantity']) {
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
            //->select(implode(',', $this->fields))
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
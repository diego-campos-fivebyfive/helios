<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Manager\InverterManager;
use Doctrine\ORM\QueryBuilder;

class InverterLoader
{
    /**
     * @var QueryBuilder
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
     * @param $maker
     * @return array
     */
    public function load(&$power, $maker)
    {
        $attempts  = 1;

        do {

            $combine = false;
            $min = ($power * $this->fdiMin);
            $max = ($power * $this->fdiMax);

            $inverters = $this->find($min, $max, $maker, 'asc');

            if (!count($inverters)) {
                for ($i = 300; $i >= 0; $i -= 15) {

                    $attempts++;

                    $inverters = $this->find(($min * ($i / 1000)), $max, $maker, 'desc');

                    if (count($inverters) >= 2) {
                        $combine = true;
                        break;
                    }
                }
            } else {
                array_splice($inverters, 1);
                $inverters[0]->quantity = 1;
            }

            if(!count($inverters)) {
                $power += 0.2;
            }

            if($combine){
                InverterCombiner::combine($inverters, $min);
            }

        }while(!count($inverters));

        return $inverters;
    }

    /**
     * @param $min
     * @param $max
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find($min, $max, $maker, $order = 'asc')
    {
        $this->qb->orderBy('i.nominalPower', $order);

        $this->qb->setParameters([
            'min' => $min,
            'max' => $max,
            'maker' => $maker
        ]);

        return $this->qb->getQuery()->getResult();
    }

    /**
     * Initialize QueryBuilder
     */
    private function init()
    {
        $qb = $this->manager->getEntityManager()->createQueryBuilder();

        $qb
            ->select('i')
            ->from($this->manager->getClass(), 'i')
            ->where($qb->expr()->between('i.nominalPower', ':min', ':max'))
            ->andWhere('i.maker = :maker');

        $this->qb = $qb;
    }
}
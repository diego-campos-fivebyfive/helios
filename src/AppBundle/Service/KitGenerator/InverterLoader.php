<?php

namespace AppBundle\Service\KitGenerator;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Manager\InverterManager;
use AppBundle\Manager\MakerManager;

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
     * @var \Doctrine\ORM\Query
     */
    private $query;

    /**
     * @var string
     */
    private $fields = 'i.id, i.mpptMin, i.mpptNumber, i.mpptMaxDcCurrent, i.nominalPower, i.maxDcVoltage';

    /**
     * @var int
     */
    private $combination = 1;
    /**
     * @var MakerManager
     */
    private $makerManager;

    /**
     * @inheritDoc
     */
    public function __construct(InverterManager $manager, MakerManager $makerManager)
    {
        $this->manager = $manager;
        $this->makerManager = $makerManager;
    }

    public function loadFromRanges($min, $max)
    {
        $makers = $this->loadMakers();

        $inverters = [];
        foreach ($makers as $maker){
            $inverters[$maker] = $this->loadInverters($min, $max, $maker);
        }

        dump($inverters); die;


        $this->refreshQuery($min, $max);

        $inverters = $this->query->getResult();

        if (!count($inverters)) {

            $this->combination = 2;

            for ($i = 0.3; $i > 0.025; $i -= 0.015) {
                sleep($this->delay);
                $inverters = $this->loadFromRanges($min * $i, $max);
                if (count($inverters) >= 2) {
                    break;
                }
            }
        }

        array_walk($inverters, function (&$inverter){
            $inverter['quantity'] = 0;
        });

        dump($inverters); die;

        return $inverters;
    }

    /**
     * @return int
     */
    public function getCombination()
    {
        return $this->combination;
    }

    /**
     * @param $min
     * @param $max
     */
    private function refreshQuery($min, $max, $maker)
    {
        if (!$this->query) {

            $qb = $this->manager->getEntityManager()->createQueryBuilder();

            $this->query =
                $qb->select($this->fields)
                    ->from(Inverter::class, 'i')
                    ->where(
                        $qb->expr()->between('i.nominalPower', ':min', ':max')
                    )
                    ->andWhere('i.maker = :maker')
                    ->orderBy('i.nominalPower', 'asc')
                    ->getQuery();
        }

        $this->query->setParameter('min', $min);
        $this->query->setParameter('max', $max);
        $this->query->setParameter('maker', $maker);
    }

    private function loadInverters($min, $max, $maker)
    {
        $qb = $this->manager->getEntityManager()->createQueryBuilder();

        $qb->select($this->fields)
            ->from($this->manager->getClass(), 'i')
            ->where(
                $qb->expr()->between('i.nominalPower', ':min', ':max')
            )
            ->andWhere('i.maker = :maker')
            ->orderBy('i.nominalPower', 'asc')
        ;

        $qb->setParameters([
            'min' => $min,
            'max' => $max,
            'maker' => $maker
        ]);

        $inverters = $qb->getQuery()->getResult();

        if(empty($inverters)){

            dump('empty');

            for ($i = 0.3; $i > 0.025; $i -= 0.015) {
                sleep($this->delay);
                $inverters = $this->loadInverters(($min * $i), $max, $maker);
                if (count($inverters) >= 2) {
                    break;
                }
            }
        }

        return $inverters;
    }

    private function loadMakers()
    {
        $qb = $this->makerManager->getEntityManager()->createQueryBuilder();

        $qb
            ->select('m.id')
            ->from($this->makerManager->getClass(), 'm')
            ->where('m.context = :context')
            ->setParameter('context', Maker::CONTEXT_INVERTER)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }
}
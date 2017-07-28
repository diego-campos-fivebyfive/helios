<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Manager\InverterManager;

class MakerDetector
{
    /**
     * @var InverterManager
     */
    private $manager;

    /**
     * MakerDetector constructor.
     * @param InverterManager $manager
     */
    function __construct(InverterManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $power
     * @return array
     */
    public function fromPower($power)
    {
        $qb = $this->manager->getEntityManager()->createQueryBuilder();

        $inverters = $qb->select('i')
            ->from(Inverter::class, 'i')
            ->orderBy('i.nominalPower', 'desc')
            ->groupBy('i.maker')
            ->where(
                $qb->expr()->gte(
                    $qb->expr()->prod('i.nominalPower', AbstractConfig::$maxInverters),
                    (float) $power
                )
            )
            ->getQuery()
            ->getResult()
        ;

        return array_map(function(InverterInterface $inverter){
            return $inverter->getMaker();
        }, $inverters);
    }
}
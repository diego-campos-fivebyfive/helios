<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Manager\InverterManager;

class MakerDetector
{
    const RETURN_IDS = 0;
    const RETURN_INSTANCES = 1;

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
     * @param array $defaults
     * @return array
     */
    public function fromDefaults(array $defaults, $returnType = self::RETURN_IDS)
    {
        $power = (float) $defaults['power'];
        $makers = $this->fromPower($power);

        if(in_array($defaults['grid_phase_number'], ['Monophasic', 'Biphasic'])){
            $triphasicMakers = $this->filterNotOnlyTriphasic();
            foreach ($makers as $key => $maker){
                if(!in_array($maker, $triphasicMakers)){
                    unset($makers[$key]);
                }
            }
        }

        if(self::RETURN_IDS == $returnType) {
            $ids = array_map(function (MakerInterface $maker) {
                return $maker->getId();
            }, array_values($makers));

            return $ids;
        }

        return $makers;
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

        return $this->sanitize($inverters);
    }

    public function filterNotOnlyTriphasic()
    {
        $em = $this->manager->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb2 = $em->createQueryBuilder();

        $qb->select('m')
            ->from(Maker::class, 'm')
            ->andWhere(
                $qb->expr()->in(
                    'm.id',
                    $qb2
                        ->select('m2.id')
                        ->from(Maker::class, 'm2')
                        ->join(Inverter::class, 'i2', 'WITH', 'm2.id = i2.maker')
                        ->where('i2.phases < :triphasic')
                        ->getQuery()
                        ->getDQL()
                )
            )
        ;

        $qb->setParameters([
            'triphasic' => 3
        ]);

       return $qb->getQuery()->getResult();
    }

    /**
     * @param array $inverters
     * @return array
     */
    private function sanitize(array $inverters)
    {
        return array_values(array_filter(array_map(function(InverterInterface $inverter){
            return $inverter->getMaker();
        }, $inverters)));
    }
}
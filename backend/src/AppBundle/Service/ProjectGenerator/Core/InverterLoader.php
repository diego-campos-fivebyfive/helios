<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Manager\InverterManager;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class InverterLoader
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class InverterLoader extends AbstractLoader
{
    /**
     * @var string
     */
    protected $properties = 'i.id, i.generatorLevels levels, i.alternative, i.phases phaseNumber, i.phaseVoltage,
        i.compatibility, i.nominalPower, i.minPowerSelection, i.maxPowerSelection, i.mpptParallel,
        i.mpptNumber, i.mpptMin, i.inProtection, i.maxDcVoltage, i.mpptMaxDcCurrent';

    /**
     * @return array
     */
    public function all()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select($this->properties)
            ->where('i.maker = :maker')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->isNull('i.minPowerSelection'),
                        $qb->expr()->lte('i.minPowerSelection', ':power')
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->isNull('i.maxPowerSelection'),
                        $qb->expr()->gte('i.maxPowerSelection', ':power')
                    )
                )
            )
            ->orderBy('i.nominalPower', 'ASC')
            ->setParameter('maker', $this->config['maker'])
            ->setParameter('power', $this->config['power']);

        return $this->formatKeys($qb->getQuery()->getResult());
    }

    /**
     * @return array
     */
    public function alternatives()
    {
        $qb2 = $this->manager->createQueryBuilder();

        $alternatives = array_map(function ($alt) {
            return current($alt);
        }, $qb2->select("DISTINCT(i.alternative)")
            ->where('i.alternative > 0')
            ->getQuery()->getResult()
        );

        if ($alternatives) {
            $qb = $this->manager->createQueryBuilder();

            $results = $qb->select($this->properties)
                ->where(
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'i.id',
                        $alternatives
                    ),
                    'i.maker != :maker'
                ))
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->orX(
                            $qb->expr()->isNull('i.minPowerSelection'),
                            $qb->expr()->lte('i.minPowerSelection', ':power')
                        ),
                        $qb->expr()->orX(
                            $qb->expr()->isNull('i.maxPowerSelection'),
                            $qb->expr()->gte('i.maxPowerSelection', ':power')
                        )
                    )
                )
                ->orderBy('i.nominalPower', 'ASC')
                ->setParameter('maker', $this->config['maker'])
                ->setParameter('power', $this->config['power'])
                ->getQuery()->getResult();

            return $this->formatKeys($results);
        }

        return [];
    }

}

<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Pricing;

use AppBundle\Manager\Pricing\RangeManager;

/**
 * This class carries multiples or a single range according to the parameters
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class RangeLoader
{
    /**
     * @var RangeManager
     */
    private $manager;

    /**
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param int|\AppBundle\Entity\Pricing\MemorialInterface $memorial
     * @param float $power
     * @param string $level
     * @param array|string $codes
     * @return array
     */
    public function load($memorial, $power, $level, $codes)
    {
        $single = false;

        if(!is_array($codes)){
            $single = true;
            $codes = [$codes];
        }

        $qb = $this->manager->createQueryBuilder();

        $qb->where($qb->expr()->in('r.code', $codes))
            ->andwhere('r.level = :level')
            ->andWhere('r.initialPower <= :power')
            ->andWhere('r.finalPower > :power')
            ->andWhere('r.memorial = :memorial')
        ;

        $qb->setParameters([
            'level' => $level,
            'power' => $power,
            'memorial' => $memorial
        ]);

        $query = $qb->getQuery();

        return $single ? $query->getOneOrNullResult() : $query->getResult();
    }
}
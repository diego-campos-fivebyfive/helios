<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Manager\Pricing\MemorialManager;

class MemorialLoader
{
    /**
     * @var MemorialManager
     */
    private $manager;

    /**
     * @param MemorialManager $manager
     */
    function __construct(MemorialManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \AppBundle\Entity\Pricing\MemorialInterface|object
     */
    public function load()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where('m.status = :status')
            ->andWhere('m.publishedAt is not null')
            ->andWhere('m.expiredAt is null')
            ->setMaxResults(1)
            ->setParameters([
                'status' => Memorial::STATUS_ENABLED
            ]);

        $memorial =$qb->getQuery()->getOneOrNullResult();

        return $memorial;
    }
}

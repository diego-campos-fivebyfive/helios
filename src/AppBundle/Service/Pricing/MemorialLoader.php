<?php

namespace AppBundle\Service\Pricing;

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
            ->andWhere('m.endAt is null')
            ->andWhere('m.startAt <= :startAt')
            ->orderBy('m.id','desc')
            ->setMaxResults(1)
            ->setParameters([
                'status' => 1,
                'startAt' => (new \DateTime())->format('Y-m-d')
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
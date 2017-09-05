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
            ->setParameters(['status' => 1]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}

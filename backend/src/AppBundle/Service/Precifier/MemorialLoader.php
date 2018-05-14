<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\MemorialManager;

/**
 * Class MemorialLoader
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
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
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function load()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where('m.status = :status')
            ->andWhere('m.publishedAt is not null')
            ->andWhere('m.expiredAt is null')
            ->setMaxResults(1)
            ->setParameters([
                'status' => Memorial::STATUS_PUBLISHED
            ]);

        $memorial = $qb->getQuery()->getOneOrNullResult();

        return $memorial;
    }
}

<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\MemorialManager;

/**
 * Class MemorialHelper
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class MemorialHelper
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
     * @return Memorial
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

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Memorial $memorial
     */
    public function syncPublishMemorial(Memorial $memorial)
    {
        if ($memorial->isPublished()){

            $qb = $this->manager->createQueryBuilder();

            $qb
                ->where('m.status = :status')
                ->andWhere(
                    $qb->expr()->notIn('m.id', ':id')
                )
                ->setParameters([
                    'status' => Memorial::STATUS_PUBLISHED,
                    'id' => $memorial->getId()
                ]);

            $memorials = $qb->getQuery()->getResult();

            /** @var Memorial $currentMemorial */
            foreach ($memorials as $currentMemorial){
                $currentMemorial->setStatus(Memorial::STATUS_EXPIRED);
                $this->manager->save($currentMemorial, false);
            }

            $this->manager->flush();
        }
    }
}

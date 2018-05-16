<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Manager\Precifier\RangeManager;

/**
 * Class RangeLoader
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class RangeHelper
{
    /** @var RangeManager */
    private $manager;

    /**
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $rangeIds
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function load($rangeIds)
    {
        $single = false;

        if(!is_array($rangeIds)){
            $single = true;
            $rangeIds = [$rangeIds];
        }

        $qb = $this->manager->createQueryBuilder();

        $qb->where(
            $qb->expr()->in('r.id', $rangeIds)
        );

        if ($single) {
            return $qb->getQuery()->getOneOrNullResult();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $memorialId
     * @return array
     */
    public function componentsIds(int $memorialId)
    {
        $families = ComponentsLoader::getFamilies();

        $componentsIds = [];

        foreach ($families as $family) {
            $qb = $this->manager->createQueryBuilder();

            $qb->select('r.componentId')
                ->where('r.family = :family')
                ->andWhere('r.memorial = :memorial')
                ->setParameters([
                    'family' => $family,
                    'memorial' => $memorialId
                ]);

            $componentsIds[$family] = array_map('current', $qb->getQuery()->getResult());
        }

        return $componentsIds;
    }
}

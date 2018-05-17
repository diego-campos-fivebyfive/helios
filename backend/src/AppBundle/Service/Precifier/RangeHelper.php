<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\RangeManager;

/**
 * Class RangeLoader
 * @package AppBundle\Service\Precifier
 *
 * @author Gianluca Bine <gian_bine@hotmail.com>
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
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
     * @param Memorial $memorial
     * @param array $families
     * @return array
     */
    public function componentsIds(Memorial $memorial, array $families)
    {
        $families = $families ? $families : ComponentsLoader::getFamilies();

        $componentsIds = [];

        foreach ($families as $family) {
            $qb = $this->manager->createQueryBuilder();

            $qb->select('r.componentId')
                ->where('r.family = :family')
                ->andWhere('r.memorial = :memorial')
                ->setParameters([
                    'family' => $family,
                    'memorial' => $memorial
                ]);

            $componentsIds[$family] = array_map('current', $qb->getQuery()->getResult());
        }

        return $componentsIds;
    }
}

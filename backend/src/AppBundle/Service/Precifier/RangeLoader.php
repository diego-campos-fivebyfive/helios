<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\RangeManager;


/**
 * Class RangeLoader
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class RangeLoader
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
}

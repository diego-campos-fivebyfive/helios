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
     * @param Memorial $memorial
     * @param $family
     * @param $componentId
     * @return Range
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function load(Memorial $memorial, $family, $componentId)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('r.memorial', $memorial->getId()),
            $qb->expr()->eq('r.family', $qb->expr()->literal($family)),
            $qb->expr()->eq('r.componentId', $componentId)
        ));

        return $qb->getQuery()->getOneOrNullResult();
    }
}

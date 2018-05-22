<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\RangeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RangeLoader
 * @package AppBundle\Service\Precifier
 *
 * @author Gianluca Bine <gian_bine@hotmail.com>
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class RangeHelper
{
    /**
     * @var ComponentsLoader
     */
    private $componentsLoader;

    /** @var RangeManager */
    private $manager;

    /**
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->manager = $container->get('precifier_range_manager');

        $this->componentsLoader = $container->get('precifier_components_loader');
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

    /**
     * @param Memorial $memorial
     * @param array $filters
     * @return array
     */
    public function filterAndFormatRanges(Memorial $memorial, array $filters)
    {
        $families = $filters['families'] ?? [];

        $groupsRanges = [];

        $groups = [];

        foreach ($families as $family) {
            if ($family === 'stringBox') {
                $family = 'string_box';
            }

            $qb = $this->manager->createQueryBuilder();

            $qb->select('r.id, r.componentId, r.costPrice, r.metadata as ranges')
                ->where('r.memorial = :memorial')
                ->andWhere('r.family = :family')
                ->setParameters([
                    'memorial' => $memorial,
                    'family' => $family
                ]);

            $ranges = $qb->getQuery()->getResult();

            if ($family === 'string_box') {
                $family = 'stringBox';
            }

            $groups[$family] = array_column($ranges, 'componentId');

            $groupsRanges[$family] = $ranges;
        }

        $groupComponents = $this->componentsLoader->loadByIds($groups);

        $level = $filters['level'] ?? '';

        $powerRanges = $filters['powerRanges'] ?? [];

        sort($powerRanges);

        $results = [];

        foreach ($groupsRanges as $family => $ranges) {

            $components = $groupComponents[$family];

            foreach ($ranges as $range) {
                $componentId = $range['componentId'];
                $rangeId = $range['id'];

                $range['code'] = $components[$componentId]['code'];
                $range['description'] = $components[$componentId]['description'];

                $levelRanges = $range['ranges'][$level];

                if ($powerRanges) {
                    $range['ranges'] = [];
                    foreach ($powerRanges as $powerRange) {
                        if (isset($levelRanges[$powerRange])) {
                            $range['ranges'][$powerRange] = $levelRanges[$powerRange];
                        }
                    }
                } else {
                    $range['ranges'] = $levelRanges;
                }

                $results[$family][$rangeId] = $range;
            }
        }

        return $results;
    }
}

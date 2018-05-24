<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\RangeManager;
use Doctrine\ORM\QueryBuilder;
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
     * @param $groups
     * @return array
     */
    public function loadByComponentsIds($groups)
    {
        $ranges = [];

        $qb = $this->manager->createQueryBuilder();

        $qb->select('r.componentId, r.family, r.metadata');

        foreach ($groups as $family => $componentsIds) {
            $qb->where(
                $qb->expr()->in('r.componentId', $componentsIds)
            )->andWhere(
                $qb->expr()->eq('r.family', $qb->expr()->literal($family))
            );

            foreach ($qb->getQuery()->getResult() as $range) {
                $ranges[] = $range;
            }
        }

        return $ranges;
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
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function filterAndFormatRanges(Memorial $memorial, array $filters)
    {
        $families = $filters['families'] ?? [];
        $level = $filters['level'] ?? '';
        $filterPowerRanges = $filters['powerRanges'] ?? [];

        sort($filterPowerRanges);

        $groupsRanges = [];

        $componentsGroupsIds = [];

        $this->loadRanges($memorial, $families, $groupsRanges, $componentsGroupsIds);

        $groupsComponents = $this->componentsLoader->loadByIds($componentsGroupsIds);

        $formatedRanges = $this->formatRanges($groupsRanges, $groupsComponents, $level, $filterPowerRanges);

        return $formatedRanges;
    }

    /**
     * @param $ranges
     * @return mixed
     */
    public function formatMarkup($ranges)
    {
        foreach ($ranges as $powerRange => $data) {
            $ranges[$powerRange]['markup'] = $data['markup'] * 100;
        }

        return $ranges;
    }

    /**
     * @param Memorial $memorial
     * @param $families
     * @param $groupsRanges
     * @param $componentsGroupsIds
     */
    private function loadRanges(Memorial $memorial, $families, &$groupsRanges, &$componentsGroupsIds)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select('r.id, r.componentId, r.costPrice, r.metadata as ranges');

        foreach ($families as $family) {

            $qb->where('r.memorial = :memorial')
                ->andWhere('r.family = :family')
                ->setParameters([
                    'memorial' => $memorial,
                    'family' => $family === 'stringBox'
                        ? 'string_box'
                        : $family
                ]);

            $ranges = $qb->getQuery()->getResult();

            $componentsGroupsIds[$family] = array_column($ranges, 'componentId');

            $groupsRanges[$family] = $ranges;
        }
    }

    /**
     * @param $groupsRanges
     * @param $groupsComponents
     * @param $level
     * @param $filterPowerRanges
     * @return array
     */
    private function formatRanges($groupsRanges, $groupsComponents, $level, $filterPowerRanges)
    {
        $formatedRanges = [];

        foreach ($groupsRanges as $family => $ranges) {

            $components = $groupsComponents[$family];

            foreach ($ranges as $range) {

                $componentId = $range['componentId'];

                $range['code'] = $components[$componentId]['code'];
                $range['description'] = $components[$componentId]['description'];

                $selectedLevelRanges = $range['ranges'][$level];

                $filteredPowerRanges = $this->filterPowerRanges($selectedLevelRanges, $filterPowerRanges);

                $formattedMarkupRanges = $this->formatMarkup($filteredPowerRanges);

                $range['ranges'] = $formattedMarkupRanges;

                $formatedRanges[$family][] = $range;
            }
        }

        return $formatedRanges;
    }

    /**
     * @param $powerRanges
     * @param $filterPowerRanges
     * @return array
     */
    private function filterPowerRanges($powerRanges, $filterPowerRanges)
    {
        if ($filterPowerRanges) {
            $ranges = [];

            foreach ($filterPowerRanges as $powerRange) {
                if (isset($powerRanges[$powerRange])) {
                    $ranges[$powerRange] = $powerRanges[$powerRange];
                }
            }

            return $ranges;
        }

        return $powerRanges;
    }
}

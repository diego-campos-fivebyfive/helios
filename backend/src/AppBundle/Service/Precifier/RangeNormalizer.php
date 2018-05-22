<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\MemorialManager;
use AppBundle\Manager\Precifier\RangeManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RangeNormalizer
 * This class resolves and normalizes ranges from memorial
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class RangeNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RangeManager
     */
    private $manager;

    /**
     * @var array
     */
    private $defaultMetadata;

    /**
     * @var string
     */
    private $memory = '512M';

    /**
     * RangeNormalizer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        // TODO: liberar esta linha se necessário
        // ini_set('memory_limit', $this->memory);

        $this->container = $container;

        $this->manager = $container->get('precifier_range_manager');

        $this->defaultMetadata = $this->defaultMetadata();
    }

    /**
     * @param Memorial $memorial
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function normalize(Memorial $memorial)
    {
        $metadata = $memorial->getMetadata();

        /** @var ComponentsLoader $componentsLoader */
        $componentsLoader = $this->container->get('precifier_components_loader');

        if (isset($metadata[Memorial::ACTION_TYPE_ADD_COMPONENT])) {

            $families = array_keys($metadata[Memorial::ACTION_TYPE_ADD_COMPONENT]);

            /** @var RangeHelper $rangesHelper */
            $rangesHelper = $this->container->get('precifier_range_helper');

            $componentsIdsOfAllRanges = $rangesHelper->componentsIds($memorial, $families);

            $componentsWithoutRange = $componentsLoader->loadByNotInIds($componentsIdsOfAllRanges);

            $this->generateRanges($memorial, $componentsWithoutRange);
        }

        if (isset($metadata[Memorial::ACTION_TYPE_REMOVE_COMPONENT])) {

            $families = array_keys($metadata[Memorial::ACTION_TYPE_REMOVE_COMPONENT]);

            $allComponents = $componentsLoader->loadAll($families);

            $this->removeRanges($memorial, $allComponents);
        }

        $memorial->setMetadata([]);

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->container->get('precifier_memorial_manager');

        $memorialManager->save($memorial);
    }

    /**
     * @param Memorial $memorial
     * @param $groups
     */
    private function generateRanges(Memorial $memorial, $groups)
    {
        foreach ($groups as $family => $componentsIds) {
            foreach ($componentsIds as $componentId => $code) {
                $this->createRange(
                    $memorial,
                    $family,
                    $componentId,
                    $code,
                    $this->defaultMetadata
                );
            }
        }

        $this->manager->flush();
    }

    /**
     * @param Memorial $memorial
     * @param $groups
     */
    private function removeRanges(Memorial $memorial, $groups)
    {
        foreach ($groups as $family => $componentsIds) {

            $componentsIds = $componentsIds ? $componentsIds : [''];

            /** @var QueryBuilder $qb */
            $qb = $this->manager->createQueryBuilder();

            $qb->where(
                $qb->expr()->notIn('r.componentId', $componentsIds)
            )->andWhere('r.family = :family')
                ->andWhere('r.memorial = :memorial')
                ->setParameters([
                    'family' => $family,
                    'memorial' => $memorial
                    ]);

            $results = $qb->getQuery()->getResult();
            /** @var Range $range */
            foreach ($results as $range) {
                $this->manager->delete($range, false);
            }
        }

        $this->manager->flush();
    }

    /**
     * @return array
     */
    private function defaultMetadata()
    {
        foreach (Memorial::getDefaultLevels(true) as $level) {

            foreach (Range::$powerRanges as $powerRange) {

                $defaultMetadata[$level][$powerRange] = [
                    'markup' => 0,
                    'price' => 0
                ];
            }
        }

        return $defaultMetadata;
    }

    /**
     * @param Memorial $memorial
     * @param $family
     * @param $componentId
     * @param $metadata
     * @param int $price
     */
    private function createRange(Memorial $memorial, $family, $componentId, $code, $metadata, $price = 0)
    {
        $range = new Range();

        $range->setMemorial($memorial);
        $range->setFamily($family);
        $range->setComponentId($componentId);
        $range->setCode($code);
        $range->setCostPrice($price);
        $range->setMetadata($metadata);

        $this->manager->save($range, false);
    }
}

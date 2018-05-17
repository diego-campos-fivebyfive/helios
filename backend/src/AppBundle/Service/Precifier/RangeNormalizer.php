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
use AppBundle\Manager\Precifier\RangeManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class RangeNormalizer
 * This class resolves and normalizes ranges from memorial
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class RangeNormalizer
{
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
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
        // TODO: liberar esta linha se necessário
        // ini_set('memory_limit', $this->memory);

        $this->manager = $manager;

        $this->defaultMetadata = $this->defaultMetadata();
    }

    /**
     * @param Memorial $memorial
     * @param $groups
     */
    private function generateRanges(Memorial $memorial, $groups)
    {
        $new = false;

        foreach ($groups as $family => $componentsIds) {

            foreach ($componentsIds as $componentId) {
                $this->createRange(
                    $memorial,
                    $family,
                    $componentId,
                    $this->defaultMetadata
                );

                $new = true;
            }
        }

        if ($new) {
            $this->manager->flush();
        }
    }

    /**
     * @param Memorial $memorial
     * @param $groups
     */
    private function excludeRanges(Memorial $memorial, $groups)
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
                $this->manager->delete($range);
            }
        }
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
    private function createRange(Memorial $memorial, $family, $componentId, $metadata, $price = 0)
    {
        $range = new Range();

        $range->setMemorial($memorial);
        $range->setFamily($family);
        $range->setComponentId($componentId);
        $range->setCode(null);
        $range->setCostPrice($price);
        $range->setMetadata($metadata);

        $this->manager->save($range, false);
    }
}

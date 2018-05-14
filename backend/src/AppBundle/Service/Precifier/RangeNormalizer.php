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
    public function normalize(Memorial $memorial, $groups)
    {
        $new = false;

        foreach ($groups as $family => $components) {
            $ids = array_keys($components);

            $has = $this->getRanges($family, $memorial, $ids);

            $without = array_diff($ids, $has);

            foreach ($without as $componentId) {
                $this->createRange(
                    $memorial,
                    $family,
                    $componentId,
                    $components[$componentId],
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
     * @param $family
     * @param Memorial $memorial
     * @param array $ids
     * @return array
     */
    private function getRanges($family, Memorial $memorial, array $ids)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select('r.componentId')
            ->where('r.family = :family')
            ->andWhere('r.memorial = :memorial')
            ->andWhere($qb->expr()->in('r.componentId', $ids))
            ->setParameters([
                'family' => $family,
                'memorial' => $memorial->getId()
            ]);

        return array_map('current', $qb->getQuery()->getResult());
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
     * @param $code
     * @param $metadata
     * @param int $price
     */
    public function createRange(Memorial $memorial, $family, $componentId, $code, $metadata, $price = 0)
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

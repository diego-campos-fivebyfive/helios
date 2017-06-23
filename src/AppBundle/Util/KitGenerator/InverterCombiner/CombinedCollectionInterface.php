<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface CombinedCollectionInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface CombinedCollectionInterface
{
    /**
     * @param CombinedInterface $combined
     * @return CombinedCollectionInterface
     */
    public function addCombined(CombinedInterface $combined);

    /**
     * @param CombinedInterface $combined
     * @return CombinedCollectionInterface
     */
    public function removeCombined(CombinedInterface $combined);

    /**
     * @return array
     */
    public function getCombinations();
}
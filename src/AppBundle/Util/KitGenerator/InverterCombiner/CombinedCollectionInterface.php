<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

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
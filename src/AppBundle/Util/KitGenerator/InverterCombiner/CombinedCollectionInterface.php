<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface CombinedCollectionInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface CombinedCollectionInterface
{
    /**
     * @param ModuleInterface $module
     * @return CombinedCollectionInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return CombinedCollectionInterface
     */
    public function getModule();

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
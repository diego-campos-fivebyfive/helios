<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Interface ProfileInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProfileInterface
{
    /**
     * @param $size
     * @return ProfileInterface
     */
    public function setSize($size);

    /**
     * @return float
     */
    public function getSize();
}
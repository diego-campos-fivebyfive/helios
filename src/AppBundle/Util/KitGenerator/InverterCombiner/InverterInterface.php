<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface InverterInterface
 */
interface InverterInterface
{
    /**
     * @param $nominalPower
     * @return InverterInterface
     */
    public function setNominalPower($nominalPower);

    /**
     * @return float
     */
    public function getNominalPower();

    /**
     * @param $quantity
     * @return InverterInterface
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();
}
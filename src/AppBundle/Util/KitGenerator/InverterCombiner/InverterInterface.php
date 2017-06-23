<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface InverterInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
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
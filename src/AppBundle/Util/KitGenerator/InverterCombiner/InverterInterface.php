<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface InverterInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface InverterInterface
{
    /**
     * @param $id
     * @return InverterInterface
     */
    public function setId($id);

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @param $name
     * @return InverterInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

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
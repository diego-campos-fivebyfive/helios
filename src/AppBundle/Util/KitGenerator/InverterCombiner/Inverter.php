<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Class Inverter
 */
class Inverter implements InverterInterface
{
    /**
     * @var float
     */
    private $nominalPower;

    /**
     * @var int
     */
    private $quantity = 1;

    /**
     * @inheritDoc
     */
    public function setNominalPower($nominalPower)
    {
        $this->nominalPower = $nominalPower;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNominalPower()
    {
        return $this->nominalPower;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
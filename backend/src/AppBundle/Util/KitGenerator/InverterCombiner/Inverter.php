<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Class Inverter
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Inverter implements InverterInterface
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

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
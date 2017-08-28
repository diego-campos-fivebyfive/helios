<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

class Profile implements ProfileInterface
{
    use MakerDefinition;

    private $id;

    private $description;

    private $size;

    private $quantity;

    /**
     * @inheritDoc
     */
    public function __construct($id = null, $description = null, $size = 0, $quantity = 0)
    {
        $this->id = $id;
        $this->description = $description;
        $this->size = $size;
        $this->quantity = $quantity;
        $this->maker    = Structure::MAKER_SICES_SOLAR;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Profile
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Profile
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return (float) $this->size;
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
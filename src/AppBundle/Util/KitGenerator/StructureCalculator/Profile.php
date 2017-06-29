<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

class Profile implements ProfileInterface
{
    private $size;

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
}
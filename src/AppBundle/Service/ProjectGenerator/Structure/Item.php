<?php

namespace AppBundle\Service\ProjectGenerator\Structure;

class Item
{
    public $type;

    public $quantity;


    private function __construct($type)
    {
        $this->type = $type;
        $this->quantity = 0;
    }

    public static function create($type)
    {
        return new self($type);
    }
}
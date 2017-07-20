<?php

namespace AppBundle\Service\ProjectGenerator\Structure;

class Profile
{
    public $id;

    public $size;

    private function __construct($id, $size)
    {
        $this->id = $id;
        $this->size = $size;
    }

    public static function create($id, $size)
    {
        return new self($id, $size);
    }
}
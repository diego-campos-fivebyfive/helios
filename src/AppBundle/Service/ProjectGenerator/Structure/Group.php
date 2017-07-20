<?php

namespace AppBundle\Service\ProjectGenerator\Structure;

class Group
{
    public $lines;

    public $modules;

    public $position;

    /**
     * Group constructor.
     */
    private function __construct($lines, $modules, $position)
    {
        $this->lines = (int) $lines;
        $this->modules = (int) $modules;
        $this->position = (int) $position;
    }

    /**
     * @param $lines
     * @param $modules
     * @param $position
     * @return Group
     */
    public static function create($lines, $modules, $position)
    {
        return new self($lines, $modules, $position);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->lines * $this->modules;
    }
}
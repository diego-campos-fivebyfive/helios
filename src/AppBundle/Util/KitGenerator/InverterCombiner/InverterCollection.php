<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

class InverterCollection implements InverterCollectionInterface
{
    private $inverters = [];

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->inverters;
    }

    /**
     * @inheritdoc
     */
    public function add(InverterInterface $inverter)
    {
        $this->inverters[] = $inverter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->inverters[$key];
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->inverters);
    }
}
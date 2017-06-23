<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface InverterCollectionInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface InverterCollectionInterface
{

    /**
     * @param InverterInterface $inverter
     * @return InverterCollectionInterface
     */
    public function add(InverterInterface $inverter);

    /**
     * @param $key
     * @return InverterInterface
     */
    public function get($key);

    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function all();
}
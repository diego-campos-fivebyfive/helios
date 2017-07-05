<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface InverterCombinerInterface
 */
interface InverterCombinerInterface
{
    /**
     * @param ModuleInterface $module
     * @return mixed
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return mixed
     */
    public function getModule();

    /**
     * @param InverterInterface $inverter
     * @return mixed
     */
    public function addInverter(InverterInterface $inverter);

    /**
     * @return mixed
     */
    public function combine();
}
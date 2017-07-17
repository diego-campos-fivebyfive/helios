<?php
/**
 * Created by PhpStorm.
 * User: claudinei
 * Date: 11/07/17
 * Time: 15:51
 */

namespace AppBundle\Util\KitGenerator\StructureCalculator;


trait MakerDefinition
{
    /**
     * @var int
     */
    protected $maker;

    /**
     * @inheritDoc
     */
    public function setMaker($maker)
    {
        $this->maker = $maker;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaker()
    {
        return $this->maker;
    }
}
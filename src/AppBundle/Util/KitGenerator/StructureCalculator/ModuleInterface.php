<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Interface ModuleInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ModuleInterface
{
    /**
     * Positions
     */
    const VERTICAL   = 0;
    const HORIZONTAL = 1;

    /**
     * Default modules per line
     */
    const MODULES_PER_LINE      = 12;

    /**
     * @param $quantity
     * @return ModuleInterface
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $cellNumber
     * @return ModuleInterface
     */
    public function setCellNumber($cellNumber);

    /**
     * @return int
     */
    public function getCellNumber();

    /**
     * @param $length
     * @return ModuleInterface
     */
    public function setPosition($length);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param $length
     * @return ModuleInterface
     */
    public function setLength($length);

    /**
     * @return float
     */
    public function getLength();

    /**
     * @param $width
     * @return ModuleInterface
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @return array
     */
    public function getGroups();

    /**
     * @return int
     */
    public function countGroups();

    /**
     * @return float
     */
    public function getDimension();

    /**
     * @return bool
     */
    public function isVertical();

    /**
     * @return bool
     */
    public function isHorizontal();

    /**
     * @return int
     */
    public function getModulesPerLine();

    /**
     * @return float|int
|z     */
    public function getMaxProfileSize();
}
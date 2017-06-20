<?php

namespace AppBundle\Util\KitGenerator;

interface StructureCalculatorInterface
{
    /**
     * Roof Types
     */
    //const ROOF_ROMAN            = 0;
    //const ROOF_AMERICAN         = 0;
    const ROOF_ROMAN_AMERICAN   = 0;
    const ROOF_CEMENT           = 1;
    const ROOF_FLAT_SLAB        = 2;
    const ROOF_SHEET_METAL      = 3;
    const ROOF_SHEET_METAL_PFM  = 4;

    /**
     * Positions
     */
    const POSITION_HORIZONTAL   = 'horizontal';
    const POSITION_VERTICAL     = 'vertical';

    /**
     * @param $position
     * @return StructureCalculatorInterface
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getPosition();

    /**
     * @param $cellNumber
     * @return StructureCalculatorInterface
     */
    public function setCellNumber($cellNumber);

    /**
     * @return int
     */
    public function getCellNumber();
}
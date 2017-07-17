<?php

namespace AppBundle\Util\KitGenerator;

interface StructureCalculatorInterface
{
    /**
     * Roof Types
     */
    //const ROOF_ROMAN            = 0;
    //const ROOF_AMERICAN         = 0;
    const ROOF_ROMAN_AMERICAN   = 'ROOF_ROMAN_AMERICAN';    //0
    const ROOF_CEMENT           = 'ROOF_CEMENT';            //1
    const ROOF_FLAT_SLAB        = 'ROOF_FLAT_SLAB';         //2
    const ROOF_SHEET_METAL      = 'ROOF_SHEET_METAL';       //3
    const ROOF_SHEET_METAL_PFM  = 'ROOF_SHEET_METAL_PFM';   //4
    const POSITION_HORIZONTAL   = 'HORIZONTAL';
    const POSITION_VERTICAL     = 'VERTICAL';

    const ITEMS                 = 'ITEMS';
    const ROOF                  = 'ROOF';
    const MODULE                = 'MODULE';
    const PROFILES              = 'PROFILES';
    const PROFILE_MIDDLE        = 'PROFILE_MIDDLE';
    const GROUPS                = 'GROUPS';

    const TERMINAL_FINAL        = 'TERMINAL_FINAL';
    const TERMINAL_INTERMEDIARY = 'TERMINAL_INTERMEDIARY';

    const FIXER_BOLT            = 'FIXER_BOLT';
    const FIXER_NUT             = 'FIXER_NUT';

    const BASE_HOOK             = 'BASE_HOOK';
    const BASE_FRICTION_TAPE    = 'BASE_FRICTION_TAPE';
    const BASE_SPEED_CLIP       = 'BASE_SPEED_CLIP';
    const BASE_SCREW_FRAME      = 'BASE_SCREW_FRAME';
    const BASE_TRIANGLE_VERTICAL    = 'BASE_TRIANGLE_VERTICAL';
    const BASE_TRIANGLE_HORIZONTAL  = 'BASE_TRIANGLE_HORIZONTAL';
    const BASE_SCREW_AUTO       = 'BASE_SCREW_AUTO';

    const JUNCTION              = 'JUNCTION';


    public static function calculate(array &$data);

    /**
     * @return array
     */
    public static function getRoofTypes();
}
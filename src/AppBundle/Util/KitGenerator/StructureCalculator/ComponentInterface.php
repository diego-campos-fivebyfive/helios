<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Interface ComponentInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface ComponentInterface
{
    const TYPE_JUNCTION = 0;
    const TYPE_END_TERMINAL = 1;
    const TYPE_IN_TERMINAL = 2;
    const TYPE_PROFILE_MP = 3;
    const TYPE_BASE = 4;
    const TYPE_SCREW_HAMMER = 5;
    const TYPE_NUT_M10 = 6;
    const TYPE_SCREW_STR = 7;
    const TYPE_SCREW_AUTO = 8;
    const TYPE_TRIANGLE = 9;
    const TYPE_TAPE = 10;


    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @return bool
     */
    public function isJunction();

    /**
     * @return bool
     */
    public function isEndTerminal();

    /**
     * @return bool
     */
    public function isInTerminal();

    /**
     * @return bool
     */
    public function isProfileMp();

    /**
     * @return bool
     */
    public function isBase();

    /**
     * @return bool
     */
    public function isScrewHammer();

    /**
     * @return bool
     */
    public function isNutM10();

    /**
     * @return bool
     */
    public function isScrewStr();

    /**
     * @return bool
     */
    public function isScrewAuto();

    /**
     * @return bool
     */
    public function isTriangle();

    /**
     * @return bool
     */
    public function isTape();


}


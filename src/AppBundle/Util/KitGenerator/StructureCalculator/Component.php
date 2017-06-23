<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Class Component
 * @author Daniel Martins <daniel@kolinalabs.com>
 */

class Component implements ComponentInterface
{

    /**
     * @var int
     */
    private $type;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Component
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isJunction()
    {
        return self::TYPE_JUNCTION == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isEndTerminal()
    {
        return self::TYPE_END_TERMINAL == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isInTerminal()
    {
        return self::TYPE_IN_TERMINAL == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isProfileMp()
    {
        return self::TYPE_PROFILE_MP == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isBase()
    {
        return self::TYPE_BASE == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isScrewHammer()
    {
        return self::TYPE_SCREW_HAMMER == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isNutM10()
    {
        return self::TYPE_NUT_M10 == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isScrewStr()
    {
        return self::TYPE_SCREW_STR == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isScrewAuto()
    {
        return self::TYPE_SCREW_AUTO == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isTriangle()
    {
        return self::TYPE_TRIANGLE == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isTape()
    {
        return self::TYPE_TAPE == $this->type;
    }


}


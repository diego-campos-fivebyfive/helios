<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Class Module
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Module implements ModuleInterface
{
    /**
     * @var int
     */
    private $cellNumber;

    /**
     * @var float
     */
    private $length;

    /**
     * @var float
     */
    private $width;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->position = self::VERTICAL;
    }

    /**
     * @inheritdoc
     */
    public function getCellNumber()
    {
        return (int) $this->cellNumber;
    }

    /**
     * @inheritdoc
     */
    public function setCellNumber($cellNumber)
    {
        $this->cellNumber = $cellNumber;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLength()
    {
        return (float) $this->length;
    }

    /**
     * @inheritdoc
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWidth()
    {
        return (float) $this->width;
    }

    /**
     * @inheritdoc
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return (int) $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritdoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        $groups = [];
        $totalModules = $this->getQuantity();
        $modulesPerLine = $this->getModulesPerLine();
        $remaining = $totalModules;

        for($i = 0; $i < ceil($totalModules / $modulesPerLine); $i++){
            $groups[$i] = $remaining;
            if($remaining > $modulesPerLine){
                $groups[$i] = $modulesPerLine;
                $remaining -= $modulesPerLine;
            }
        }

        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function countGroups()
    {
        return count($this->getGroups());
    }

    /**
     * @inheritDoc
     */
    public function getDimension()
    {
        return $this->isVertical() ? $this->getWidth() : $this->getLength();
    }

    /**
     * @inheritDoc
     */
    public function isVertical()
    {
        return self::VERTICAL == $this->position;
    }

    /**
     * @inheritDoc
     */
    public function isHorizontal()
    {
        return self::HORIZONTAL == $this->position;
    }

    /**
     * @inheritDoc
     */
    public function getModulesPerLine()
    {
        $modulesPerLine = self::MODULES_PER_LINE;

        if($this->quantity > 52){
            $modulesPerLine = 18;
        }

        if($this->isHorizontal()){

            $modulesPerLine = 6;

            if(60 == $this->cellNumber){
                $modulesPerLine = 7;

                if($this->quantity > 52){
                    $modulesPerLine = 11;
                }
            }
        }

        return $modulesPerLine;
    }

    /**
     * @inheritDoc
     */
    public function getMaxProfileSize()
    {
        $quantity = $this->quantity;

        $profileSize = 0;
        if ($quantity <= 52) {
            $profileSize = 1;
            if ($quantity <= 26) {
                $profileSize = 2;
                if ($quantity <= 12) {
                    $profileSize = 3;
                }
            }
        }

        return $profileSize;
    }
}
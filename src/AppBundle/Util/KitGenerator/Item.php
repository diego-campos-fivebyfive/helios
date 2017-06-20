<?php
/**
 * Created by PhpStorm.
 * User: Fabio
 * Date: 20/06/2017
 * Time: 17:48
 */

namespace AppBundle\Util\KitGenerator;


class Item implements ItemInterface
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
    private $lines;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $position;

    /**
     * @return int
     */
    public function getCellNumber()
    {
        return $this->cellNumber;
    }

    /**
     * @param int $cellNumber
     * @return Item
     */
    public function setCellNumber($cellNumber)
    {
        $this->cellNumber = $cellNumber;
        return $this;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param float $length
     * @return Item
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     * @return Item
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param int $lines
     * @return Item
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return Item
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }


    public function getMaxProfileSize()
    {
        // TODO: Implement getMaxProfileSize() method.
    }

    public function getMaxNumberPerLine()
    {
        // TODO: Implement getMaxNumberPerLine() method.
    }
}
<?php


namespace AppBundle\Util\KitGenerator\InverterCombiner;
/**
 * Class Module
 * @author Daniel Martins <daniel@kolinalabs.com>
 */

class Module implements ModuleInterface
{

    /**
     * @var int
     */
    private $id;

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
    private $cellNumber;

    /**
     * @var float
     */
    private $openCircuitVoltage;

    /**
     * @var float
     */
    private $voltageMaxPower;

    /**
     * @var float
     */
    private $tempCoefficientVoc;

    /**
     * @var float
     */
    private $maxPower;

    /**
     * @var float
     */
    private $shortCircuitCurrent;

    /**
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Module
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Module
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
     * @return Module
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getCellNumber()
    {
        return $this->cellNumber;
    }

    /**
     * @param int $cellNumber
     * @return Module
     */
    public function setCellNumber($cellNumber)
    {
        $this->cellNumber = $cellNumber;
        return $this;
    }

    /**
     * @return float
     */
    public function getOpenCircuitVoltage()
    {
        return $this->openCircuitVoltage;
    }

    /**
     * @param float $openCircuitVoltage
     * @return Module
     */
    public function setOpenCircuitVoltage($openCircuitVoltage)
    {
        $this->openCircuitVoltage = $openCircuitVoltage;
        return $this;
    }

    /**
     * @return float
     */
    public function getVoltageMaxPower()
    {
        return $this->voltageMaxPower;
    }

    /**
     * @param float $voltageMaxPower
     * @return Module
     */
    public function setVoltageMaxPower($voltageMaxPower)
    {
        $this->voltageMaxPower = $voltageMaxPower;
        return $this;
    }

    /**
     * @return float
     */
    public function getTempCoefficientVoc()
    {
        return $this->tempCoefficientVoc;
    }

    /**
     * @param float $tempCoefficientVoc
     * @return Module
     */
    public function setTempCoefficientVoc($tempCoefficientVoc)
    {
        $this->tempCoefficientVoc = $tempCoefficientVoc;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxPower()
    {
        return $this->maxPower;
    }

    /**
     * @param float $maxPower
     * @return Module
     */
    public function setMaxPower($maxPower)
    {
        $this->maxPower = $maxPower;
        return $this;
    }

    /**
     * @return float
     */
    public function getShortCircuitCurrent()
    {
        return $this->shortCircuitCurrent;
    }

    /**
     * @param float $shortCircuitCurrent
     * @return Module
     */
    public function setShortCircuitCurrent($shortCircuitCurrent)
    {
        $this->shortCircuitCurrent = $shortCircuitCurrent;
        return $this;
    }


}
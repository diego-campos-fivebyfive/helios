<?php

namespace AppBundle\Service\ProjectGenerator;

class Module
{
    use ObjectTrait;

    /**
     * @var int
     */
    public $id;

    /**
     * @var float
     */
    public $power;

    /**
     * @var float
     */
    public $maxPower;

    /**
     * @var float
     */
    public $openCircuitVoltage;

    /**
     * @var float
     */
    public $voltageMaxPower;

    /**
     * @var float
     */
    public $tempCoefficientVoc;

    /**
     * @var float
     */
    public $shortCircuitCurrent;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * Module constructor.
     */
    private function __construct($id, $power, $maxPower, $openCircuitVoltage, $voltageMaxPower, $tempCoefficientVoc, $shortCircuitCurrent)
    {
        $this->id = $id;
        $this->power = $power;
        $this->maxPower = $maxPower;
        $this->openCircuitVoltage = $openCircuitVoltage;
        $this->voltageMaxPower = $voltageMaxPower;
        $this->tempCoefficientVoc = $tempCoefficientVoc;
        $this->shortCircuitCurrent = $shortCircuitCurrent;
    }

    /**
     * @param $id
     * @param $power
     * @param $maxPower
     * @param $openCircuitVoltage
     * @param $voltageMaxPower
     * @param $tempCoefficientVoc
     * @param $shortCircuitCurrent
     * @return Module
     */
    public static function create($id, $power, $maxPower, $openCircuitVoltage, $voltageMaxPower, $tempCoefficientVoc, $shortCircuitCurrent)
    {
        return new self($id, $power, $maxPower, $openCircuitVoltage, $voltageMaxPower, $tempCoefficientVoc, $shortCircuitCurrent);
    }
}
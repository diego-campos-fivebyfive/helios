<?php

namespace AppBundle\Service\ProjectGenerator;

class Inverter
{
    use ObjectTrait;

    /**
     * @var int
     */
    public $id;

    /**
     * @var float
     */
    public $nominalPower;

    /**
     * @var float
     */
    public $maxDcVoltage;

    /**
     * @var int
     */
    public $mpptMin;

    /**
     * @var float
     */
    public $mpptMaxCcCurrent;

    /**
     * @var int
     */
    public $mpptNumber;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var int
     */
    public $serial;

    /**
     * @var int
     */
    public $parallel;

    /**
     * Inverter constructor.
     */
    private function __construct($id, $nominalPower, $maxDcVoltage, $mpptMin, $mpptMaxDcCurrent, $mpptNumber, $quantity)
    {
        $this->id = $id;
        $this->nominalPower = $nominalPower;
        $this->maxDcVoltage = $maxDcVoltage;
        $this->mpptMin = $mpptMin;
        $this->mpptMaxCcCurrent = $mpptMaxDcCurrent;
        $this->mpptNumber = $mpptNumber;
        $this->quantity = $quantity;
        $this->serial = 0;
        $this->parallel = 0;
    }

    /**
     * @param $id
     * @param $nominalPower
     * @param $maxDcVoltage
     * @param $mpptMin
     * @param $mpptMaxDcCurrent
     * @param $mpptNumber
     * @return Inverter
     */
    public static function create($id, $nominalPower, $maxDcVoltage, $mpptMin, $mpptMaxDcCurrent, $mpptNumber, $quantity = 1)
    {
        return new self($id, $nominalPower, $maxDcVoltage, $mpptMin, $mpptMaxDcCurrent, $mpptNumber, $quantity);
    }

    /**
     * @return int
     */
    public function count()
    {
        return ($this->serial * $this->parallel) * $this->quantity;
    }
}
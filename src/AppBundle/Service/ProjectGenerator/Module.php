<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\Structure\Group;

class Module
{
    use ObjectTrait;

    const VERTICAL = 0;
    const HORIZONTAL = 1;

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
    public $cellNumber;

    /**
     * @var int
     */
    public $position = 0;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * Module constructor.
     */
    private function __construct(
        $id,
        $position,
        $power,
        $maxPower,
        $openCircuitVoltage,
        $voltageMaxPower,
        $tempCoefficientVoc,
        $shortCircuitCurrent,
        $cellNumber
    )
    {
        $this->id = $id;
        $this->position = $position;
        $this->power = $power;
        $this->maxPower = $maxPower;
        $this->openCircuitVoltage = $openCircuitVoltage;
        $this->voltageMaxPower = $voltageMaxPower;
        $this->tempCoefficientVoc = $tempCoefficientVoc;
        $this->shortCircuitCurrent = $shortCircuitCurrent;
        $this->cellNumber = $cellNumber;
    }

    /**
     * @param $id
     * @param $power
     * @param $position
     * @param $maxPower
     * @param $openCircuitVoltage
     * @param $voltageMaxPower
     * @param $tempCoefficientVoc
     * @param $shortCircuitCurrent
     * @return Module
     */
    public static function create(
        $id,
        $position,
        $power,
        $maxPower,
        $openCircuitVoltage,
        $voltageMaxPower,
        $tempCoefficientVoc,
        $shortCircuitCurrent,
        $cellNumber
    )
    {
        return new self(
            $id,
            $position,
            $power,
            $maxPower,
            $openCircuitVoltage,
            $voltageMaxPower,
            $tempCoefficientVoc,
            $shortCircuitCurrent,
            $cellNumber
        );
    }

    public function groups()
    {
        $limit = $this->position == self::VERTICAL ? 20 : 12 ;

        $groups = [];
        if (0 != ($this->quantity % $limit) && ($this->quantity > $limit)) {

            $groups[] = Group::create(((int) floor($this->quantity / $limit)), $limit, $this->position);
            $groups[] = Group::create(1,((($this->quantity / $limit) - floor($this->quantity / $limit)) * $limit), $this->position);

            /*
            $groups[0]['lines'] = (int) floor($this->quantity / $limit);
            $groups[0]['modules'] = $limit;
            $groups[0]['position'] = $this->position;
            $groups[1]['lines'] = 1;
            $groups[1]['modules'] = (int) (($this->quantity / $limit) - floor($this->quantity / $limit)) * $limit;
            $groups[1]['position'] = $this->position;
            */
        } else {

            $groups[] = Group::create(((int) ceil($this->quantity / $limit)), (int) $this->quantity / ceil($this->quantity / $limit), $this->position);
            /*$groups[0]['lines'] = (int) ceil($this->quantity / $limit);
            $groups[0]['modules'] = (int) $this->quantity / ceil($this->quantity / $limit);
            $groups[0]['position'] = $this->position;*/
        }

        return $groups;
    }
}
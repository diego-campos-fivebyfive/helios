<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Class Combined
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
class Combined implements CombinedInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $nominalPower;

    /**
     * @var float
     */
    private $maxDcVoltage;

    /**
     * @var float
     */
    private $mpptMin;

    /**
     * @var float
     */
    private $mpptMaxDcCurrent;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $serial;

    /**
     * @var int
     */
    private $parallel;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Combined
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNominalPower()
    {
        return $this->nominalPower;
    }

    /**
     * @param mixed $nominalPower
     * @return Combined
     */
    public function setNominalPower($nominalPower)
    {
        $this->nominalPower = $nominalPower;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxDcVoltage()
    {
        return $this->maxDcVoltage;
    }

    /**
     * @param mixed $maxDcVoltage
     * @return Combined
     */
    public function setMaxDcVoltage($maxDcVoltage)
    {
        $this->maxDcVoltage = $maxDcVoltage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMpptMin()
    {
        return $this->mpptMin;
    }

    /**
     * @param mixed $mpptMin
     * @return Combined
     */
    public function setMpptMin($mpptMin)
    {
        $this->mpptMin = $mpptMin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMpptMaxDcCurrent()
    {
        return $this->mpptMaxDcCurrent;
    }

    /**
     * @param mixed $mpptMaxDcCurrent
     * @return Combined
     */
    public function setMpptMaxDcCurrent($mpptMaxDcCurrent)
    {
        $this->mpptMaxDcCurrent = $mpptMaxDcCurrent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return Combined
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * @param mixed $serial
     * @return Combined
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParallel()
    {
        return $this->parallel;
    }

    /**
     * @param mixed $parallel
     * @return Combined
     */
    public function setParallel($parallel)
    {
        $this->parallel = $parallel;
        return $this;
    }


}
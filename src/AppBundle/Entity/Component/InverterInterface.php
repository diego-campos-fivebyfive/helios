<?php

namespace AppBundle\Entity\Component;

interface InverterInterface extends ComponentInterface
{
    /**
     * @param $maxDcPower
     * @return mixed
     */
    public function setMaxDcPower($maxDcPower);

    /**
     * @return mixed
     */
    public function getMaxDcPower();

    /**
     * @param $maxDcVoltage
     * @return mixed
     */
    public function setMaxDcVoltage($maxDcVoltage);

    /**
     * @return mixed
     */
    public function getMaxDcVoltage();
    
    /**
     * @param $nominalPower
     * @return InverterInterface
     */
    public function setNominalPower($nominalPower);

    /**
     * @return float
     */
    public function getNominalPower();

    /**
     * @param $mpptMaxDcCurrent
     * @return InverterInterface
     */
    public function setMpptMaxDcCurrent($mpptMaxDcCurrent);

    /**
     * @return float
     */
    public function getMpptMaxDcCurrent();
    
    /**
     * @param $maxEfficiency
     * @return mixed
     */
    public function setMaxEfficiency($maxEfficiency);

    /**
     * @return mixed
     */
    public function getMaxEfficiency();
    
    /**
     * @param $mpptMax
     * @return mixed
     */
    public function setMpptMax($mpptMax);

    /**
     * @return mixed
     */
    public function getMpptMax();

    /**
     * @param $mpptMin
     * @return mixed
     */
    public function setMpptMin($mpptMin);

    /**
     * @return mixed
     */
    public function getMpptMin();

    /**
     * @param $mpptNumber
     * @return mixed
     */
    public function setMpptNumber($mpptNumber);

    /**
     * @return mixed
     */
    public function getMpptNumber();

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();
}

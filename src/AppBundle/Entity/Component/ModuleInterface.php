<?php

namespace AppBundle\Entity\Component;

interface ModuleInterface extends ComponentInterface
{
    /**
     * @param $maxPower
     * @return ModuleInterface
     */
    public function setMaxPower($maxPower);

    /**
     * @return float
     */
    public function getMaxPower();
    
    /**
     * @param $voltageMaxPower
     * @return ModuleInterface
     */
    public function setVoltageMaxPower($voltageMaxPower);

    /**
     * @return float
     */
    public function getVoltageMaxPower();

    /**
     * @param $currentMaxPower
     * @return ModuleInterface
     */
    public function setCurrentMaxPower($currentMaxPower);

    /**
     * @return float
     */
    public function getCurrentMaxPower();

    /**
     * @param $openCircuitVoltage
     * @return ModuleInterface
     */
    public function setOpenCircuitVoltage($openCircuitVoltage);

    /**
     * @return float
     */
    public function getOpenCircuitVoltage();

    /**
     * @param $shortCircuitCurrent
     * @return ModuleInterface
     */
    public function setShortCircuitCurrent($shortCircuitCurrent);

    /**
     * @return float
     */
    public function getShortCircuitCurrent();

    /**
     * @param $efficiency
     * @return ModuleInterface
     */
    public function setEfficiency($efficiency);

    /**
     * @return float
     */
    public function getEfficiency();

    /**
     * @param $temperatureOperation
     * @return ModuleInterface
     */
    public function setTemperatureOperation($temperatureOperation);

    /**
     * @return int
     */
    public function getTemperatureOperation();

    /**
     * @param $tempCoefficientMaxPower
     * @return ModuleInterface
     */
    public function setTempCoefficientMaxPower($tempCoefficientMaxPower);

    /**
     * @return float
     */
    public function getTempCoefficientMaxPower();

    /**
     * @param $tempCoefficientShortCircuitCurrent
     * @return ModuleInterface
     */
    public function setTempCoefficientShortCircuitCurrent($tempCoefficientShortCircuitCurrent);

    /**
     * @return float
     */
    public function getTempCoefficientShortCircuitCurrent();

    /**
     * @param $tempCoefficientOpenCircuitVoltage
     * @return ModuleInterface
     */
    public function setTempCoefficientOpenCircuitVoltage($tempCoefficientOpenCircuitVoltage);

    /**
     * @return float
     */
    public function getTempCoefficientOpenCircuitVoltage();

    /**
     * @param $cellType
     * @return ModuleInterface
     */
    public function setCellType($cellType);

    /**
     * @return string
     */
    public function getCellType();
}

<?php

namespace AppBundle\Service\SolarEnergy\Core;

interface AreaInterface
{
    const INCLINATION_MIN = 0;
    const INCLINATION_MAX = 180;
    const ORIENTATION_MIN = -359;
    const ORIENTATION_MAX = 359;
    const FLIP_ORIENTATION = 180;

    /**
     * This value correspond a specific identification from area
     * Note: This value must be unique
     *
     * @return mixed | int | string
     */
    public function getId();

    /**
     * This value correspond a inverter efficiency in decimal
     * Note: value converted, example: this (0.85) in place it (85%)
     *
     * @return float
     */
    public function getInverterEfficiency();

    /**
     * This value correspond a module max power in watts
     * Note: value without symbol
     *
     * @return int
     */
    public function getModuleMaxPower();

    /**
     * This value correspond a module efficiency in decimal, mode STC
     * Note: value converted, example: this (0.1616) in place it (16.16%)
     *
     * @return float
     */
    public function getModuleEfficiency();

    /**
     * This value correspond a module temperature, mode NOCT
     * Note: value without symbol, example: 45º Celsius is only 45
     *
     * @return int
     */
    public function getModuleTemperature();

    /**
     * This value correspond a module temperature coefficient in decimal, [maxPower]
     * Note: value without symbol, example: -0.41
     *
     * @return float
     */
    public function getModuleCoefficientTemperature();

    /**
     * This value correspond a string number in current area distribution
     *
     * @return int
     */
    public function getStringNumber();

    /**
     * This value correspond a real distribution modules by string
     *
     * @return int
     */
    public function getStringDistribution();

    /**
     * @param float $inclinationDegree
     * @return AreaInterface
     */
    public function setInclinationDegree($inclinationDegree);

    /**
     * @return float
     */
    public function getInclinationDegree();

    /**
     * @param float $orientationDegree
     * @return AreaInterface
     */
    public function setOrientationDegree($orientationDegree);

    /**
     * @return float
     */
    public function getOrientationDegree();

    /**
     * @param float $inclinationRadian
     * @return AreaInterface
     */
    public function setInclinationRadian($inclinationRadian);

    /**
     * @return float
     */
    public function getInclinationRadian();

    /**
     * @param float $orientationRadian
     * @return AreaInterface
     */
    public function setOrientationRadian($orientationRadian);

    /**
     * @return float
     */
    public function getOrientationRadian();

    /**
     * @param $loss
     * @return AreaInterface
     */
    public function setInverterSideLoss($loss);

    /**
     * @return float
     */
    public function getInverterSideLoss();

    /**
     * @param $loss
     * @return AreaInterface
     */
    public function setModuleSideLoss($loss);

    /**
     * @return float
     */
    public function getModuleSideLoss();

    /**
     * This value correspond a product between $stringNumber and $stringDistribution
     *
     * @return int
     */
    public function totalModules();

    /**
     * This value correspond a expression result
     * countModules() * $moduleMaxPower / ($moduleEfficiency * 1000);
     *
     * @return float
     */
    public function totalArea();

    /**
     * This value correspond a expression result
     * countModules() * $moduleMaxPower / 1000
     *
     * @return float
     */
    public function totalPower();

    /**
     * TODO: Add metadata validator on this process
     * @param array $metadata
     * @return mixed
     */
    public function setMetadata(array $metadata);

    /**
     * @return array
     */
    public function getMetadata();
}
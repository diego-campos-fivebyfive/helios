<?php

namespace AppBundle\Service\Support\Project;

interface PowerEstimatorInterface
{
    const TEMPERATURE_OPERATION = 45;
    const MODULE_EFFICIENCY = 0.1616;
    const INVERTER_EFFICIENCY = 0.97;
    const COEFFICIENT = -0.41;

    /**
     * Input: Energy Consumption (kWh)
     *
     * @param $consumption
     * @return PowerEstimatorInterface
     */
    public function setConsumption($consumption);

    /**
     * @return float
     */
    public function getConsumption();

    /**
     * global radiation average from NASA database
     *
     * @param $globalRadiation
     * @return PowerEstimatorInterface
     */
    public function setGlobalRadiation($globalRadiation);

    /**
     * @return float
     */
    public function getGlobalRadiation();

    /**
     * Top of atmosphere radiation average from NASA database
     *
     * @param $atmosphereRadiation
     * @return PowerEstimatorInterface
     */
    public function setAtmosphereRadiation($atmosphereRadiation);

    /**
     * @return float
     */
    public function getAtmosphereRadiation();

    /**
     * Air temperature average from NASA database
     *
     * @param $airTemperature
     * @return PowerEstimatorInterface
     */
    public function setAirTemperature($airTemperature);

    /**
     * @return float
     */
    public function getAirTemperature();

    /**
     * Default self::TEMPERATURE_OPERATION
     *
     * @param $temperatureOperation
     * @return PowerEstimatorInterface
     */
    public function setTemperatureOperation($temperatureOperation);

    /**
     * @return float
     */
    public function getTemperatureOperation();

    /**
     * Default self::MODULE_EFFICIENCY
     *
     * @param $moduleEfficiency
     * @return PowerEstimatorInterface
     */
    public function setModuleEfficiency($moduleEfficiency);

    /**
     * @return float
     */
    public function getModuleEfficiency();

    /**
     * Default self::COEFFICIENT
     *
     * @param $coefficient
     * @return PowerEstimatorInterface
     */
    public function setCoefficient($coefficient);

    /**
     * @return float
     */
    public function getCoefficient();

    /**
     * Default self::INVERTER_EFFICIENCY
     *
     * @param $inverterEfficiency
     * @return PowerEstimatorInterface
     */
    public function setInverterEfficiency($inverterEfficiency);

    /**
     * @return float
     */
    public function getInverterEfficiency();

    /**
     * Available only after call calculate() method
     *
     * @return float
     */
    public function getFactor();

    /**
     * Available only after call calculate() method
     *
     * @return float
     */
    public function getArea();

    /**
     * Available only after call calculate() method
     * 
     * @return float
     */
    public function getPower();

    /**
     * @return PowerEstimatorInterface
     */
    public function calculate();
}
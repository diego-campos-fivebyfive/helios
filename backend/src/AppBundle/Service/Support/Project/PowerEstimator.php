<?php

namespace AppBundle\Service\Support\Project;

class PowerEstimator implements PowerEstimatorInterface
{
    /**
     * @var float
     */
    private $consumption;

    /**
     * @var float
     */
    private $globalRadiation;

    /**
     * @var float
     */
    private $atmosphereRadiation;

    /**
     * @var float
     */
    private $airTemperature;

    /**
     * @var float
     */
    private $temperatureOperation;

    /**
     * @var float
     */
    private $coefficient;

    /**
     * @var float
     */
    private $moduleEfficiency;

    /**
     * @var float
     */
    private $inverterEfficiency;

    /**
     * @var float
     */
    private $factor;

    /**
     * @var float
     */
    private $area;

    /**
     * @var float
     */
    private $power;

    /**
     * PowerForecaster constructor.
     */
    function __construct()
    {
        $this->inverterEfficiency = self::INVERTER_EFFICIENCY;
        $this->moduleEfficiency = self::MODULE_EFFICIENCY;
        $this->coefficient = self::COEFFICIENT;
        $this->temperatureOperation = self::TEMPERATURE_OPERATION;
    }

    /**
     * @inheritDoc
     */
    public function setConsumption($consumption)
    {
        $this->consumption = $consumption;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConsumption()
    {
        return $this->consumption;
    }

    /**
     * @inheritDoc
     */
    public function setGlobalRadiation($globalRadiation)
    {
        $this->globalRadiation = $globalRadiation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGlobalRadiation()
    {
        return $this->globalRadiation;
    }

    /**
     * @inheritDoc
     */
    public function setAtmosphereRadiation($atmosphereRadiation)
    {
        $this->atmosphereRadiation = $atmosphereRadiation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAtmosphereRadiation()
    {
        return $this->atmosphereRadiation;
    }

    /**
     * @inheritDoc
     */
    public function setAirTemperature($airTemperature)
    {
        $this->airTemperature = $airTemperature;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAirTemperature()
    {
        return $this->airTemperature;
    }

    /**
     * @inheritDoc
     */
    public function setTemperatureOperation($temperatureOperation)
    {
        $this->temperatureOperation = $temperatureOperation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemperatureOperation()
    {
        return $this->temperatureOperation;
    }

    /**
     * @inheritDoc
     */
    public function setModuleEfficiency($moduleEfficiency)
    {
        $this->moduleEfficiency = $moduleEfficiency;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModuleEfficiency()
    {
        return $this->moduleEfficiency;
    }

    /**
     * @inheritDoc
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }
    
    /**
     * @inheritDoc
     */
    public function setInverterEfficiency($inverterEfficiency)
    {
        $this->inverterEfficiency = $inverterEfficiency;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverterEfficiency()
    {
        return $this->inverterEfficiency;
    }

    /**
     * @inheritDoc
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @inheritDoc
     */
    public function calculate()
    {
        $this->factor = ($this->consumption * 12) / ($this->globalRadiation * 365);

        $kt = $this->globalRadiation / $this->atmosphereRadiation;
        $tc = $this->airTemperature + ((219+(832*($kt)))*(($this->temperatureOperation-20)/800));
        $moduleEfficiencyFinal = $this->moduleEfficiency * (1-((-($this->coefficient)/100)*($tc-25)));

        $this->area = $this->factor / ($moduleEfficiencyFinal * $this->inverterEfficiency);

        $this->power = ($this->area * $this->moduleEfficiency * 1000) / 1000;

        return $this;
    }
}
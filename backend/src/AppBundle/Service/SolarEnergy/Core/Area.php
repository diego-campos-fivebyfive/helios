<?php

namespace AppBundle\Service\SolarEnergy\Core;

final class Area implements AreaInterface
{
    /**
     * @var mixed | int | string
     */
    private $id;

    /**
     * @var float
     */
    private $inverterEfficiency;

    /**
     * @var int
     */
    private $moduleMaxPower;

    /**
     * @var float
     */
    private $moduleEfficiency;

    /**
     * @var int
     */
    private $moduleTemperature;

    /**
     * @var float
     */
    private $moduleCoefficientTemperature;

    /**
     * @var int
     */
    private $stringNumber;

    /**
     * @var int
     */
    private $stringDistribution;

    /**
     * @var float
     */
    private $inclinationDegree;

    /**
     * @var float
     */
    private $orientationDegree;

    /**
     * @var float
     */
    private $inclinationRadian;

    /**
     * @var float
     */
    private $orientationRadian;

    /**
     * @var float
     */
    private $inverterSideLoss;

    /**
     * @var float
     */
    private $moduleSideLoss;

    /**
     * @var array
     */
    private $metadata;

    function __construct($id,
                         $inverterEfficiency,
                         $moduleMaxPower,
                         $moduleEfficiency,
                         $moduleTemperature,
                         $moduleCoefficientTemperature,
                         $stringNumber,
                         $stringDistribution)
    {

        $this->id = $id;
        $this->inverterEfficiency = (float) $inverterEfficiency;
        $this->moduleMaxPower = (int) $moduleMaxPower;
        $this->moduleEfficiency = (float) $moduleEfficiency;
        $this->moduleTemperature = (int) $moduleTemperature;
        $this->moduleCoefficientTemperature = (float) $moduleCoefficientTemperature;
        $this->stringNumber = (int) $stringNumber;
        $this->stringDistribution = (int) $stringDistribution;

        $this->inverterSideLoss = 0.0;
        $this->moduleSideLoss = 0.0;

        $this->checkDefaults();
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
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
    public function getModuleMaxPower()
    {
        return $this->moduleMaxPower;
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
    public function getModuleTemperature()
    {
        return $this->moduleTemperature;
    }

    /**
     * @inheritDoc
     */
    public function getModuleCoefficientTemperature()
    {
        return $this->moduleCoefficientTemperature;
    }

    /**
     * @inheritDoc
     */
    public function getStringNumber()
    {
        return $this->stringNumber;
    }

    /**
     * @inheritDoc
     */
    public function getStringDistribution()
    {
        return $this->stringDistribution;
    }

    /**
     * @inheritDoc
     */
    public function setInclinationDegree($inclinationDegree)
    {
        $this->inclinationDegree = (float) $inclinationDegree;
        $this->inclinationRadian = deg2rad($this->inclinationDegree);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInclinationDegree()
    {
        return $this->inclinationDegree;
    }

    /**
     * @inheritDoc
     */
    public function setOrientationDegree($orientationDegree)
    {
        $this->orientationDegree = (float) $orientationDegree;
        $this->orientationRadian = deg2rad($this->orientationDegree + self::FLIP_ORIENTATION);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrientationDegree()
    {
        return $this->orientationDegree;
    }

    /**
     * @inheritDoc
     */
    public function setInclinationRadian($inclinationRadian)
    {
        $this->inclinationRadian = (float) $inclinationRadian;
        $this->inclinationDegree = rad2deg($this->inclinationRadian);
    }

    /**
     * @inheritDoc
     */
    public function getInclinationRadian()
    {
        return $this->inclinationRadian;
    }

    /**
     * @inheritDoc
     */
    public function setOrientationRadian($orientationRadian)
    {
        $this->orientationRadian = (float) $orientationRadian;
        $this->orientationDegree = rad2deg($this->orientationRadian);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrientationRadian()
    {
        return $this->orientationRadian;
    }

    /**
     * @inheritDoc
     */
    public function setInverterSideLoss($loss)
    {
        $this->inverterSideLoss = $loss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverterSideLoss()
    {
        return $this->inverterSideLoss;
    }

    /**
     * @inheritDoc
     */
    public function setModuleSideLoss($loss)
    {
        $this->moduleSideLoss = $loss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModuleSideLoss()
    {
        return $this->moduleSideLoss;
    }

    /**
     * @inheritDoc
     */
    public function totalModules()
    {
        return $this->stringNumber * $this->stringDistribution;
    }

    /**
     * @inheritDoc
     */
    public function totalArea()
    {
        return $this->totalModules() * $this->moduleMaxPower / ($this->moduleEfficiency * 1000);
    }

    /**
     * @inheritDoc
     */
    public function totalPower()
    {
        return $this->totalModules() * $this->moduleMaxPower / 1000;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    private function checkDefaults()
    {
        if(0 == $this->stringNumber) $this->stringNumber = 1;
        if(0 == $this->stringDistribution) $this->stringDistribution = 1;
        $this->id = md5(uniqid(time()));
    }
}
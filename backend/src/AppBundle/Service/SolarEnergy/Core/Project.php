<?php

namespace AppBundle\Service\SolarEnergy\Core;

use AppBundle\Service\SolarEnergy\Nasa\AirTemperatureInterface;
use AppBundle\Service\SolarEnergy\Nasa\DaylightHoursInterface;
use AppBundle\Service\SolarEnergy\Nasa\NasaYearInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationAtmosphereInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationDiffuseInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationGlobalInterface;
use AppBundle\Service\SolarEnergy\Nasa\SolarDeclinationInterface;
use AppBundle\Service\SolarEnergy\Nasa\SolarNoonInterface;

class Project implements ProjectInterface
{
    /**
     * @var float
     */
    private $latDegree;

    /**
     * @var float
     */
    private $latRadian;

    /**
     * @var float
     */
    private $lngDegree;

    /**
     * @var float
     */
    private $lngRadian;

    /**
     * @var RadiationGlobalInterface
     */
    private $radiationGlobal;

    /**
     * @var RadiationDiffuseInterface
     */
    private $radiationDiffuse;

    /**
     * @var RadiationAtmosphereInterface
     */
    private $radiationAtmosphere;

    /**
     * @var AirTemperatureInterface
     */
    private $airTemperature;

    /**
     * @var DaylightHoursInterface
     */
    private $daylightHours;

    /**
     * @var SolarNoonInterface
     */
    private $solarNoon;

    /**
     * @var SolarDeclinationInterface
     */
    private $solarDeclination;

    /**
     * @var array
     */
    private $areas = [];

    private $isComputed = false;

    /**
     * @inheritDoc
     */
    public function setLatDegree($latDegree)
    {
        $this->latDegree = (float) $latDegree;
        $this->latRadian = deg2rad($this->latDegree);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLatDegree()
    {
        return $this->latDegree;
    }

    /**
     * @inheritDoc
     */
    public function setLngDegree($lngDegree)
    {
        $this->lngDegree = (float) $lngDegree;
        $this->lngRadian = deg2rad($this->lngDegree);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLngDegree()
    {
        return $this->lngDegree;
    }

    /**
     * @inheritDoc
     */
    public function setLatRadian($latRadian)
    {
        $this->latRadian = (float) $latRadian;
        $this->latDegree = rad2deg($this->latRadian);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLatRadian()
    {
        return $this->latRadian;
    }

    /**
     * @inheritDoc
     */
    public function setLngRadian($lngRadian)
    {
        $this->lngRadian = (float) $lngRadian;
        $this->lngDegree = rad2deg($this->lngRadian);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLngRadian()
    {
        return $this->lngRadian;
    }

    /**
     * @inheritDoc
     */
    public function setRadiationGlobal(RadiationGlobalInterface $radiationGlobal)
    {
        $this->radiationGlobal = $radiationGlobal;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRadiationGlobal()
    {
        return $this->radiationGlobal;
    }

    /**
     * @inheritDoc
     */
    public function setRadiationDiffuse(RadiationDiffuseInterface $radiationDiffuse)
    {
        $this->radiationDiffuse = $radiationDiffuse;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRadiationDiffuse()
    {
        return $this->radiationDiffuse;
    }

    /**
     * @inheritDoc
     */
    public function setRadiationAtmosphere(RadiationAtmosphereInterface $radiationAtmosphere)
    {
        $this->radiationAtmosphere = $radiationAtmosphere;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRadiationAtmosphere()
    {
        return $this->radiationAtmosphere;
    }

    /**
     * @inheritDoc
     */
    public function setAirTemperature(AirTemperatureInterface $airTemperature)
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
    public function setDaylightHours(DaylightHoursInterface $daylightHours)
    {
        $this->daylightHours = $daylightHours;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDaylightHours()
    {
        return $this->daylightHours;
    }

    /**
     * @inheritDoc
     */
    public function setSolarNoon(SolarNoonInterface $solarNoon)
    {
        $this->solarNoon = $solarNoon;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSolarNoon()
    {
        return $this->solarNoon;
    }

    /**
     * @inheritDoc
     */
    public function setSolarDeclination(SolarDeclinationInterface $solarDeclination)
    {
        $this->solarDeclination = $solarDeclination;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSolarDeclination()
    {
        return $this->solarDeclination;
    }

    /**
     * @inheritDoc
     */
    public function getSoloReflectance()
    {
        return NasaYearInterface::SOLO_REFLECTANCE;
    }

    /**
     * @inheritDoc
     */
    public function hasArea(AreaInterface $area)
    {
        return array_key_exists($area->getId(), $this->areas);
    }

    /**
     * @inheritDoc
     */
    public function addArea(AreaInterface $area)
    {
        $this->validateArea($area);

        if(!$this->hasArea($area))
            //$this->areas[$area->getId()] = $area;
            $this->areas[] = $area;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeArea(AreaInterface $area)
    {
        if($this->hasArea($area))
            unset($this->areas[$area->getId()]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @inheritDoc
     */
    public function isComputable()
    {
        // Force computing
        $this->radiationDiffuse = 'isComputed';
        $this->radiationAtmosphere = 'isComputed';
        $this->daylightHours = 'isComputed';
        $this->solarNoon = 'isComputed';
        $this->solarDeclination = 'isComputed';

        foreach(get_object_vars($this) as $property => $value){
            if($property != 'isComputed' && !$value){
                return false; break;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isComputed($isComputed = null)
    {
        if(is_bool($isComputed)){
            $this->isComputed = $isComputed;
            return $this;
        }

        return $this->isComputed;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        $metadata = [];
        foreach($this->areas as $area){
            $metadata[$area->getId()] = $area->getMetadata();
        }

        return ['areas' => $metadata];
    }

    private function validateArea(AreaInterface $area)
    {
        if(!$area->getId())
            throw new \InvalidArgumentException('Area without attribute identifier');
    }
}
<?php

namespace AppBundle\Service\SolarEnergy\Core;

use AppBundle\Service\SolarEnergy\Nasa\AirTemperatureInterface;
use AppBundle\Service\SolarEnergy\Nasa\DaylightHoursInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationAtmosphereInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationDiffuseInterface;
use AppBundle\Service\SolarEnergy\Nasa\RadiationGlobalInterface;
use AppBundle\Service\SolarEnergy\Nasa\SolarDeclinationInterface;
use AppBundle\Service\SolarEnergy\Nasa\SolarNoonInterface;

interface ProjectInterface
{
    /**
     * @param float $latDegree
     * @return ProjectInterface
     */
    public function setLatDegree($latDegree);

    /**
     * @return float
     */
    public function getLatDegree();

    /**
     * @param float $lngDegree
     * @return ProjectInterface
     */
    public function setLngDegree($lngDegree);

    /**
     * @return float
     */
    public function getLngDegree();

    /**
     * @param float $latRadian
     * @return ProjectInterface
     */
    public function setLatRadian($latRadian);

    /**
     * @return float
     */
    public function getLatRadian();

    /**
     * @param float $lngRadian
     * @return ProjectInterface
     */
    public function setLngRadian($lngRadian);

    /**
     * @return float
     */
    public function getLngRadian();
    
    /**
     * @param RadiationGlobalInterface $radiationGlobal
     * @return ProjectInterface
     */
    public function setRadiationGlobal(RadiationGlobalInterface $radiationGlobal);

    /**
     * @return RadiationGlobalInterface
     */
    public function getRadiationGlobal();

    /**
     * @param RadiationDiffuseInterface $radiationDiffuse
     * @return ProjectInterface
     */
    public function setRadiationDiffuse(RadiationDiffuseInterface $radiationDiffuse);

    /**
     * @return RadiationDiffuseInterface
     */
    public function getRadiationDiffuse();

    /**
     * @param RadiationAtmosphereInterface $radiationAtmosphere
     * @return ProjectInterface
     */
    public function setRadiationAtmosphere(RadiationAtmosphereInterface $radiationAtmosphere);

    /**
     * @return RadiationAtmosphereInterface
     */
    public function getRadiationAtmosphere();

    /**
     * @param AirTemperatureInterface $airTemperature
     * @return ProjectInterface
     */
    public function setAirTemperature(AirTemperatureInterface $airTemperature);

    /**
     * @return AirTemperatureInterface
     */
    public function getAirTemperature();

    /**
     * @param DaylightHoursInterface $daylightHours
     * @return ProjectInterface
     */
    public function setDaylightHours(DaylightHoursInterface $daylightHours);

    /**
     * @return DaylightHoursInterface
     */
    public function getDaylightHours();

    /**
     * @param SolarNoonInterface $solarNoon
     * @return ProjectInterface
     */
    public function setSolarNoon(SolarNoonInterface $solarNoon);

    /**
     * @return SolarNoonInterface
     */
    public function getSolarNoon();

    /**
     * @param SolarDeclinationInterface $solarDeclination
     * @return ProjectInterface
     */
    public function setSolarDeclination(SolarDeclinationInterface $solarDeclination);

    /**
     * @return SolarDeclinationInterface
     */
    public function getSolarDeclination();

    /**
     * @return float
     */
    public function getSoloReflectance();

    /**
     * @param AreaInterface $area
     * @return ProjectInterface
     */
    public function addArea(AreaInterface $area);

    /**
     * @param AreaInterface $area
     * @return ProjectInterface
     */
    public function removeArea(AreaInterface $area);

    /**
     * @return array
     */
    public function getAreas();

    /**
     * @param AreaInterface $area
     * @return bool
     */
    public function hasArea(AreaInterface $area);

    /**
     * @return bool
     */
    public function isComputed($isComputed = null);

    /**
     * @return bool
     */
    public function isComputable();

    /**
     * @return array
     */
    public function getMetadata();
}
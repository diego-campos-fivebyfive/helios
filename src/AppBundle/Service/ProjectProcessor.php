<?php

namespace AppBundle\Service;

use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInterface;

use AppBundle\Entity\Component\KitComponentInterface;
use AppBundle\Entity\Project\NasaProviderInterface;
use AppBundle\Entity\Project\ProjectModuleInterface;
use AppBundle\Service\SolarEnergy\Core\Area;
use AppBundle\Service\SolarEnergy\Core\Project;
use AppBundle\Service\SolarEnergy\Core\Processor;
use AppBundle\Service\SolarEnergy\Nasa\AirTemperature;
use AppBundle\Service\SolarEnergy\Nasa\RadiationGlobal;

class ProjectProcessor
{
    /**
     * @var NasaProviderInterface
     */
    private $nasaProvider;

    function __construct(NasaProviderInterface $nasaProvider)
    {
        $this->nasaProvider = $nasaProvider;
    }

    public function process(ProjectInterface $project)
    {
        $latitude = $project->getLatitude();
        $longitude = $project->getLongitude();

        /** Create Energy Project */
        $energyProject = new Project();

        $energyProject
            ->setLatDegree($latitude)
            ->setLngDegree($longitude)
        ;

        /** @var ProjectArea $projectArea */
        foreach ($project->getAreas() as $projectArea){

            $projectInverter = $projectArea->getProjectInverter();
            $projectModule = $projectArea->getProjectModule();

            $inverter = $projectInverter->getInverter();
            $module = $projectModule->getModule();


            /** Create area */
            $area = new Area(
                null,
                $inverter->getMaxEfficiency(),
                $module->getMaxPower(),
                $module->getEfficiency(),
                $module->getTemperatureOperation(),
                $module->getTempCoefficientMaxPower(),
                $projectArea->getStringNumber(),
                $projectArea->getModuleString()
            );

            $area
                ->setInclinationDegree($projectArea->getInclination())
                ->setOrientationDegree($projectArea->getOrientation())
                ->setInverterSideLoss($projectInverter->getLoss())
                ->setModuleSideLoss($projectArea->getLoss())
            ;

            $energyProject->addArea($area);
        }

        /*
        foreach($projectInverters as $projectInverter){

            $projectModules = $projectInverter->getModules();
            
            /** @var KitComponentInterface $kitInverter *
            $kitInverter = $projectInverter->getInverter();

            $inverter = $kitInverter->getInverter();

            /** Inverter data *
            $inverterEfficiency = $inverter->getMaxEfficiency();
            
            foreach($projectModules as $projectModule){
                if($projectModule instanceof ProjectModuleInterface) {
                    
                    $kitModule = $projectModule->getModule();

                    $module = $kitModule->getModule();

                    /** Module data *
                    $moduleMaxPower = $module->getMaxPower();
                    $moduleEfficiency = $module->getEfficiency();
                    $moduleTemperature = $module->getTemperatureOperation();
                    $moduleCoefficientTemperature = $module->getTempCoefficientMaxPower();

                    /** Area data *
                    $stringNumber = $projectModule->getStringNumber();
                    $stringDistribution = $projectModule->getModuleString();
                    $inclinationDegree = $projectModule->getInclination();
                    $orientationDegree = $projectModule->getOrientation();
                    $inverterSideLoss = $projectInverter->getLoss();
                    $moduleSideLoss = $projectModule->getLoss();

                    /** Create area *
                    $area = new Area(
                        null,
                        $inverterEfficiency,
                        $moduleMaxPower,
                        $moduleEfficiency,
                        $moduleTemperature,
                        $moduleCoefficientTemperature,
                        $stringNumber,
                        $stringDistribution
                    );

                    $area
                        ->setInclinationDegree($inclinationDegree)
                        ->setOrientationDegree($orientationDegree)
                        ->setInverterSideLoss($inverterSideLoss)
                        ->setModuleSideLoss($moduleSideLoss)
                    ;

                    $energyProject->addArea($area);
                }
            }
        }
        
        $latitude = $project->getLatitude();
        $longitude = $project->getLongitude();

        $energyProject
            ->setLatDegree($latitude)
            ->setLngDegree($longitude)
        ;*/


        $radiationGlobal = new RadiationGlobal(
            $this->nasaProvider->radiationGlobal($latitude, $longitude)
        );

        /*$radiationDiffuse = new RadiationDiffuse(
            $this->nasaProvider->radiationDiffuse($latitude, $longitude)
        );

        $radiationAtmosphere = new RadiationAtmosphere(
            $this->nasaProvider->radiationAtmosphere($latitude, $longitude)
        );*/

        $airTemperature = new AirTemperature(
            $this->nasaProvider->airTemperature($latitude, $longitude)
        );

        /*$daylightHours = new DaylightHours(
            $this->nasaProvider->daylightHours($latitude)
        );

        $solarNoon = new SolarNoon(
            $this->nasaProvider->solarNoon($longitude)
        );*/

        $energyProject
            ->setRadiationGlobal($radiationGlobal)
            /*->setRadiationDiffuse($radiationDiffuse)
            ->setRadiationAtmosphere($radiationAtmosphere)
            ->setDaylightHours($daylightHours)
            ->setSolarNoon($solarNoon)*/
            ->setAirTemperature($airTemperature)
            //->setSolarDeclination(new SolarDeclination())
        ;

        $processor = new Processor($energyProject);

        $processor->compute();

        $metadata = $energyProject->getMetadata();

        $metadata['total'] = [
            'kwh_year' => 0,
            'kwh_month' => 0,
            'kwh_kwp_year' => 0,
            'kwh_kwp_month' => 0
        ];

        foreach ($metadata['areas'] as $area) {
            $metadata['total']['kwh_year'] += $area['kwh_year'];
            $metadata['total']['kwh_month'] += $area['kwh_month'];
            $metadata['total']['kwh_kwp_year'] += $area['kwh_kwp_year'];
            $metadata['total']['kwh_kwp_month'] += $area['kwh_kwp_month'];
        }

        return $metadata;
    }
}
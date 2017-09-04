<?php

namespace AppBundle\Service;

use AppBundle\Entity\Component\ProjectAreaInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\Tax;
use AppBundle\Entity\Financial\TaxInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverterInterface;
use AppBundle\Entity\Project\ProjectModuleInterface;
use AppBundle\Service\Support\Project\AreaDebugger;
use AppBundle\Service\Support\Project\PowerEstimator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $consumption
     * @return float
     * @throws \Exception
     */
    public function estimatePower($latitude, $longitude, $consumption)
    {
        $nasaProvider = $this->getNasaProvider();

        $globalRadiation = $nasaProvider->radiationGlobal($latitude, $longitude);
        $atmosphereRadiation = $nasaProvider->radiationAtmosphere($latitude, $longitude);
        $airTemperature = $nasaProvider->airTemperature($latitude, $longitude);

        if (!empty($globalRadiation) && !empty($atmosphereRadiation) && !empty($airTemperature)) {

            $avgGlobal = array_sum($globalRadiation) / count($globalRadiation);
            $avgAtmosphere = array_sum($atmosphereRadiation) / count($atmosphereRadiation);
            $avgAirTemperature = array_sum($airTemperature) / count($airTemperature);

            $estimator = new PowerEstimator();
            $estimator
                ->setConsumption($consumption)
                ->setGlobalRadiation($avgGlobal)
                ->setAtmosphereRadiation($avgAtmosphere)
                ->setAirTemperature($avgAirTemperature);

            return $estimator->calculate()->getPower();
        }

        throw new \Exception('The coordinates did not provide data for calculation');
    }


    public function debugArea(ProjectAreaInterface &$projectArea)
    {
        if ($projectArea->getProjectInverter() && $projectArea->getProjectModule()) {

            $projectInverter = $projectArea->getProjectInverter();
            $project = $projectInverter->getProject();

            $inverter = $projectInverter->getInverter();
            $module = $projectArea->getProjectModule()->getModule();

            $latitude = $project->getLatitude();
            $longitude = $project->getLongitude();

            $nasaProvider = $this->getNasaProvider();

            $globalRadiation = $nasaProvider->radiationGlobal($latitude, $longitude);
            $atmosphereRadiation = $nasaProvider->radiationAtmosphere($latitude, $longitude);
            $airTemperatureMin = $nasaProvider->airTemperatureMin($latitude, $longitude);
            $airTemperatureMax = $nasaProvider->airTemperatureMax($latitude, $longitude);

            $global = $this->sanitizeMinMax($globalRadiation);
            $atmosphere = $this->sanitizeMinMax($atmosphereRadiation);

            $temperatureMin = $this->sanitizeMinMax($airTemperatureMin);
            $temperatureMax = $this->sanitizeMinMax($airTemperatureMax);
            $temperature = ['min' => $temperatureMin['min'], 'max' => $temperatureMax['max']];

            $metadata = [
                'module' => [
                    'stc_power_max' => $module->getMaxPower(),
                    'stc_vmp' => $module->getVoltageMaxPower(),
                    'stc_imp' => $module->getCurrentMaxPower(),
                    'stc_voc' => $module->getOpenCircuitVoltage(),
                    'temp_noct' => $module->getTemperatureOperation(),
                    'coef_voc' => $module->getTempCoefficientVoc()
                ],
                'inverter' => [
                    'nominal_power' => $inverter->getNominalPower(),
                    'max_dc_power' => $inverter->getMaxDcPower(),
                    'max_dc_voltage' => $inverter->getMaxDcVoltage(),
                    'max_dc_current' => $inverter->getMpptMaxDcCurrent(),
                    'mppt_min' => $inverter->getMpptMin(),
                    'mppt_max' => $inverter->getMpptMax(),
                    'mppt_number' => $inverter->getMpptNumber()
                ],
                'global' => $global,
                'atmosphere' => $atmosphere,
                'temperature' => $temperature,
                'mppt_factor' => $projectArea->getMpptFactor(),
                'n_string' => $projectArea->getStringNumber(),
                'n_mod_string' => $projectArea->getModuleString()
            ];

            $debugger = new AreaDebugger();

            $debugger->setMetadata($metadata);

            $debugger->debug();

            $metadataOperation = $debugger->getResult();

            $this->hydrateAreaMetadataOperation($metadataOperation);

            $projectArea->setMetadata($metadataOperation);

            $manager = $this->getManager();

            $manager->persist($projectArea);
            $manager->flush();

            return $metadataOperation;
        }

        return null;
    }

    /**
     * @deprecated
     * @param ProjectInterface $project
     */
    public function processProject(ProjectInterface $project)
    {
        /** @var ProjectProcessor $processor */
        $processor = $this->container->get('app.project_processor');
        $metadata = $processor->process($project);

        if (array_key_exists('areas', $metadata)) {

            $project->setMetadata($metadata);

            $this->getProjectManager()->save($project);
        }
    }


    /**
     * Hydrate values base calculation result
     * Determine scale boundaries
     *
     * @param array $metadata
     */
    private function hydrateAreaMetadataOperation(array &$metadata)
    {
        # MPPT
        $mpptDecrease = 100;
        $mpptIncrease = 100;

        $mpptMin = $metadata['mppt']['min'];
        $mpptMax = $metadata['mppt']['max'];
        $mpptOffset = $mpptMin - $mpptDecrease;
        if ($mpptOffset < 0) $mpptOffset = 0;

        $mpptLimit = $mpptMax + $mpptIncrease;
        $mpptInterval = $mpptLimit - $mpptOffset;

        $mpptPercentOffset = (($mpptMin - $mpptOffset) / ($mpptLimit - $mpptOffset)) * 100;
        $mpptPercentLimit = (($mpptLimit - $mpptMax) / ($mpptLimit - $mpptOffset)) * 100;
        $mpptPercentCenter = (($mpptMax - $mpptMin) / ($mpptLimit - $mpptOffset)) * 100;

        $metadata = array_merge_recursive($metadata, [
            'mppt' => [
                'offset' => $mpptOffset,
                'limit' => $mpptLimit,
                'interval' => $mpptInterval,
                'percentOffset' => $mpptPercentOffset,
                'percentCenter' => $mpptPercentCenter,
                'percentLimit' => $mpptPercentLimit
            ]
        ]);

        # VOLTAGE
        $voltageOffset = 0;
        $voltageLimit = round($metadata['voltage']['max_dc_voltage'] * 1.5);

        $metadata['voltage']['offset'] = $voltageOffset;
        $metadata['voltage']['limit'] = $voltageLimit;
        $metadata['voltage']['percent'] = ($metadata['voltage']['max_dc_voltage'] * 100) / $voltageLimit;

        # CURRENT
        $currentOffset = 0;
        $currentLimit = round($metadata['current']['max_dc_current'] * 1.2);

        $metadata['current']['offset'] = $currentOffset;
        $metadata['current']['limit'] = $currentLimit;
        $metadata['current']['percent'] = ($metadata['current']['max_dc_current'] * 100) / $currentLimit;
        $metadata['current']['step'] = $this->createSingleStep($metadata['current']['max_dc_current'], 5);

        # POWER
        $powerOffset = 0;
        $powerLimit = $metadata['power']['warning_tolerance'];
        $powerPercentOffset = ($metadata['power']['max_dc_operation'] * 100) / $powerLimit;
        $powerPercentCenter = ($metadata['power']['warning_tolerance'] * 100) / $powerLimit;

        $metadata['power']['offset'] = $powerOffset;
        $metadata['power']['limit'] = $powerLimit;
        $metadata['power']['percentOffset'] = $powerPercentOffset;
        $metadata['power']['percentCenter'] = ($powerPercentCenter - $powerPercentOffset);
        $metadata['power']['step'] = $this->createSingleStep($metadata['power']['max_dc_operation'], 2);
    }

    /**
     * @param $base
     * @param $index
     * @return float
     */
    private function createSingleStep($base, $index)
    {
        $a = $base / $index;
        $b = $base / ceil($a);

        return ($base / $b);
    }

    /**
     * @return \AppBundle\Entity\Project\NasaProviderInterface|object
     */
    private function getNasaProvider()
    {
        return $this->container->get('app.nasa_provider');
    }

    /**
     * @param array $data
     * @return array
     */
    private function sanitizeMinMax(array $data)
    {
        $range = ['min' => $data[1], 'max' => $data[1]];
        foreach ($data as $value) {
            if ($value < $range['min']) $range['min'] = $value;
            if ($value > $range['max']) $range['max'] = $value;
        }

        return $range;
    }

    /**
     * @return \AppBundle\Manager\ProjectManager|object
     */
    private function getProjectManager()
    {
        return $this->container->get('project_manager');
    }

    /**
     * @return \AppBundle\Manager\CustomerManager|object
     */
    private function getAccountManager()
    {
        return $this->container->get('customer_manager');
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    private function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }
}
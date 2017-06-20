<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\Component\KitComponentInterface;

interface ProjectModuleInterface
{
    const INCLINATION_MIN = 0;
    const INCLINATION_MAX = 180;
    const ORIENTATION_MIN = -359;
    const ORIENTATION_MAX = 359;

    const ERROR_OUT_OF_RANGE = 'The reported value is outside of the acceptable range';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param ProjectInverterInterface $inverter
     * @return ProjectModuleInterface
     */
    public function setInverter(ProjectInverterInterface $inverter = null);

    /**
     * @return ProjectInverterInterface
     */
    public function getInverter();

    /**
     * @param KitComponentInterface $module
     * @return ProjectModuleInterface
     */
    public function setModule(KitComponentInterface $module);

    /**
     * @return KitComponentInterface
     */
    public function getModule();

    /**
     * @param $inclination
     * @return ProjectModuleInterface
     */
    public function setInclination($inclination);

    /**
     * @return int
     */
    public function getInclination();

    /**
     * @param $orientation
     * @return ProjectModuleInterface
     */
    public function setOrientation($orientation);

    /**
     * @return int
     */
    public function getOrientation();

    /**
     * @param $stringNumber
     * @return ProjectModuleInterface
     */
    public function setStringNumber($stringNumber);

    /**
     * @return int
     */
    public function getStringNumber();

    /**
     * @param $moduleString
     * @return ProjectModuleInterface
     */
    public function setModuleString($moduleString);

    /**
     * @return int
     */
    public function getModuleString();

    /**
     * Returns expression result by (stringNumber * moduleString)
     * @return int
     */
    public function countModules();
    
    /**
     * @param $loss
     * @return ProjectModuleInterface
     */
    public function setLoss($loss);

    /**
     * Loss between Module and Inverter
     * @return string
     */
    public function getLoss();

    /**
     * Return result for expression (PowerModule * ModPerString * NroStrings)
     * @return float
     */
    public function getPower();

    /**
     * @return float
     */
    public function getTotalArea();

    /**
     * Return index consider all modules in inverter combination
     * @return int
     */
    public function getIndex();

    /**
     * @return mixed
     */
    public function getMpptFactor();

    /**
     * @return string
     */
    public function getMpptName();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return bool
     */
    public function isComputable();

    /**
     * @return array
     */
    public static function getRange($key = null);

    /**
     * @param array $metadata
     * @return ProjectModuleInterface
     */
    public function setMetadataOperation(array $metadata);

    /**
     * @return array
     */
    public function getMetadataOperation();

    /**
     * @return array
     */
    public function getSnapshot();

    /**
     * @return float
     */
    public function getUnitPriceSale();

    /**
     * @return float
     */
    public function getPriceSale();
}
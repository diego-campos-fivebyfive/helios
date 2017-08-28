<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

/**
 * Interface ProjectAreaInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectAreaInterface
{
    /**
     * @param ProjectInverterInterface $projectInverter
     * @return ProjectAreaInterface
     */
    public function setProjectInverter(ProjectInverterInterface $projectInverter);

    /**
     * @return ProjectInverterInterface
     */
    public function getProjectInverter();

    /**
     * @param ProjectModuleInterface $projectModule
     * @return ProjectAreaInterface
     */
    public function setProjectModule(ProjectModuleInterface $projectModule);

    /**
     * @return ProjectModuleInterface
     */
    public function getProjectModule();

    /**
     * @param $inclination
     * @return ProjectAreaInterface
     */
    public function setInclination($inclination);

    /**
     * @return int
     */
    public function getInclination();

    /**
     * @param $orientation
     * @return ProjectAreaInterface
     */
    public function setOrientation($orientation);

    /**
     * @return int
     */
    public function getOrientation();

    /**
     * @param $stringNumber
     * @return ProjectAreaInterface
     */
    public function setStringNumber($stringNumber);

    /**
     * @return int
     */
    public function getStringNumber();

    /**
     * @param $moduleString
     * @return ProjectAreaInterface
     */
    public function setModuleString($moduleString);

    /**
     * @return int
     */
    public function getModuleString();

    /**
     * @param $loss
     * @return ProjectAreaInterface
     */
    public function setLoss($loss);

    /**
     * @return string
     */
    public function getLoss();

    /**
     * @return int
     */
    public function getIndex();

    /**
     * @return string
     */
    public function getMpptName();

    /**
     * @return int
     */
    public function getMpptFactor();

    /**
     * @param array $metadata
     * @return ProjectAreaInterface
     */
    public function setMetadata(array $metadata);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @return float
     */
    public function getPower();

    /**
     * @return float
     */
    public function getArea();

    /**
     * @return int
     */
    public function countModules();

    /**
     * @return bool
     */
    public function isConfigured();
}
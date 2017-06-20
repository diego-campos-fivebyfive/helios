<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\Component\KitComponentInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ProjectInverterInterface
{
    /**
     * @param ProjectInterface $project
     * @return ProjectInverterInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @param KitComponentInterface $inverter
     * @return ProjectInverterInterface
     */
    public function setInverter(KitComponentInterface $inverter);

    /**
     * @return KitComponentInterface
     */
    public function getInverter();

    /**
     * @param MpptOperationInterface $operation
     * @return ProjectInverterInterface
     */
    public function setOperation(MpptOperationInterface $operation);

    /**
     * @return MpptOperationInterface
     */
    public function getOperation();

    /**
     * @param ProjectModuleInterface $module
     * @return ProjectInverterInterface
     */
    public function addModule(ProjectModuleInterface $module);

    /**
     * @param ProjectModuleInterface $module
     * @return ProjectInverterInterface
     */
    public function removeModule(ProjectModuleInterface $module);

    /**
     * @return ArrayCollection
     */
    public function getModules();

    /**
     * @param $loss
     * @return ProjectInverterInterface
     */
    public function setLoss($loss);

    /**
     * Loss between Inverter and Connect Network
     * @return string
     */
    public function getLoss();

    /**
     * @return array
     */
    public function getSnapshot();

    /**
     * Return sum of power from related modules
     * 
     * @return float
     */
    public function getPower();

    /**
     * @return array
     */
    public function getMetadataOperation();
    
    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return array
     */
    public function toArray();
}
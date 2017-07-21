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
 * Interface ProjectInverterInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectInverterInterface extends ProjectElementInterface
{
    /**
     * @param InverterInterface $inverter
     * @return ProjectInverterInterface
     */
    public function setInverter(InverterInterface $inverter);

    /**
     * @return InverterInterface
     */
    public function getInverter();

    /**
     * @param $operation
     * @return ProjectInverterInterface
     */
    public function setOperation($operation);

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @param $loss
     * @return ProjectInverterInterface
     */
    public function setLoss($loss);

    /**
     * @return float
     */
    public function getLoss();

    /**
     * @return float
     */
    public function getPower();

    /**
     * @param $serial
     * @return ProjectInverterInterface
     */
    public function setSerial($serial);

    /**
     * @return int
     */
    public function getSerial();

    /**
     * @param $parallel
     * @return ProjectInverterInterface
     */
    public function setParallel($parallel);

    /**
     * @return int
     */
    public function getParallel();

    /**
     * @return bool
     */
    public function operationIsChanged();

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @param ProjectAreaInterface $projectArea
     * @return ProjectInverterInterface
     */
    public function addProjectArea(ProjectAreaInterface $projectArea);

    /**
     * @param ProjectAreaInterface $projectArea
     * @return ProjectInverterInterface
     */
    public function removeProjectArea(ProjectAreaInterface $projectArea);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectAreas();
}
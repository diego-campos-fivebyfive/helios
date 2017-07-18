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
 * Interface ProjectModuleInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectModuleInterface extends ProjectElementInterface
{
    /**
     * Positions
     */
    const POSITION_VERTICAL = 0;
    const POSITION_HORIZONTAL = 1;

    /**
     * @param ModuleInterface $module
     * @return ProjectModuleInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * @param ProjectAreaInterface $projectArea
     * @return ProjectModuleInterface
     */
    public function addProjectArea(ProjectAreaInterface $projectArea);

    /**
     * @param ProjectAreaInterface $projectArea
     * @return ProjectModuleInterface
     */
    public function removeProjectArea(ProjectAreaInterface $projectArea);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectAreas();

    /**
     * @return array
     */
    public function getDistribution();

    /**
     * @param $position
     * @return ProjectModuleInterface
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param array $groups
     * @return ProjectModuleInterface
     */
    public function setGroups(array $groups);

    /**
     * @return array
     */
    public function getGroups();
}
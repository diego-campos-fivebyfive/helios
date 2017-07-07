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
}
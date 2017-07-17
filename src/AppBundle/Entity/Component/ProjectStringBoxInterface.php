<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 17/07/17
 * Time: 18:02
 */

namespace AppBundle\Entity\Component;

/**
 * Interface ProjectStringBoxInterface
 *
 * @author João Zaqueu <joaozaqueu@kolinalabs.com>
 */
interface ProjectStringBoxInterface extends ProjectElementInterface
{
    /**
     * @param StringBoxInterface $stringBox
     * @return ProjectStringBoxInterface
     */
    public function setStringBox(StringBoxInterface $stringBox);

    /**
     * @return StringBoxInterface
     */
    public function getStringBox();

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
}
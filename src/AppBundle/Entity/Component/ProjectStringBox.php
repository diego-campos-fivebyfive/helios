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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectModule
 *
 * @author João Zaqueu <joaozaqueu@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_string_box")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectStringBox implements ProjectStringBoxInterface
{
    use ProjectElementTrait;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectStringBoxs")
     */
    private $project;

    /**
     * @var StringBoxInterface
     *
     * @ORM\ManyToOne(targetEntity="ProjectArea")
     */
    private $stringBox;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectArea", mappedBy="projectStringBox", cascade={"persist", "remove"})
     */
    private $projectAreas;

    /**
     * ProjectStringBox constructor.
     */
    public function __construct()
    {
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
        $this->projectAreas = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectStringBox($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return StringBoxInterface
     */
    public function getStringBox()
    {
        return $this->stringBox;
    }

    /**
     * @inheritDoc
     */
    public function setStringBox($stringBox)
    {
        $this->stringBox = $stringBox;
    }

    public function addProjectArea(ProjectAreaInterface $projectArea)
    {
        if(!$this->projectAreas->contains($projectArea)){
            $this->projectAreas->add($projectArea);

            if(!$projectArea->getProjectStringBox()){
                $projectArea->setProjectStringBox($this);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectArea(ProjectAreaInterface $projectArea)
    {
        if($this->projectAreas->contains($projectArea)){
            $this->projectAreas->removeElement($projectArea);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectAreas()
    {
        return $this->projectAreas;
    }

}
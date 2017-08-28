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
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_module")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectModule implements ProjectModuleInterface
{
    use ProjectElementTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $groups;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectModules")
     */
    private $project;

    /**
     * @var ModuleInterface
     *
     * @ORM\ManyToOne(targetEntity="Module")
     */
    private $module;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectArea", mappedBy="projectModule", cascade={"persist"})
     */
    private $projectAreas;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->position      = self::POSITION_VERTICAL;
        $this->groups        = [];
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
        $this->projectAreas  = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        $count = 0;
        foreach ($this->projectAreas as $projectArea){
            $count += $projectArea->countModules();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectModule($this);

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
     * @inheritDoc
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    public function getDistribution()
    {
        $applied = 0;
        foreach ($this->projectAreas as $projectArea){
            $applied += $projectArea->countModules();
        }

        return [
            'available' => $this->quantity,
            'configured' => $applied
        ];
    }

    /**
     * @inheritDoc
     */
    public function addProjectArea(ProjectAreaInterface $projectArea)
    {
        if(!$this->projectAreas->contains($projectArea)){
            $this->projectAreas->add($projectArea);

            if(!$projectArea->getProjectModule()){
                $projectArea->setProjectModule($this);
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
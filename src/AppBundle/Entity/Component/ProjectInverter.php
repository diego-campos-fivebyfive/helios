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
 * ProjectInverter
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_inverter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectInverter implements ProjectInverterInterface
{
    use ProjectElementTrait;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="loss", type="decimal", precision=10, scale=2)
     */
    private $loss;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectInverters")
     */
    private $project;

    /**
     * @var InverterInterface
     *
     * @ORM\ManyToOne(targetEntity="Inverter")
     */
    private $inverter;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectArea", mappedBy="projectInverter", cascade={"persist"})
     */
    private $projectAreas;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->loss          = 0;
        $this->operation     = 1;
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
        $this->projectAreas  = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectInverter($this);

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
    public function setInverter(InverterInterface $inverter)
    {
        $this->inverter = $inverter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverter()
    {
        return $this->inverter;
    }

    /**
     * @inheritDoc
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @inheritDoc
     */
    public function setLoss($loss)
    {
        $this->loss = $loss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLoss()
    {
        return $this->loss;
    }

    /**
     * @inheritDoc
     */
    public function addProjectArea(ProjectAreaInterface $projectArea)
    {
        if(!$this->projectAreas->contains($projectArea)){
            $this->projectAreas->add($projectArea);

            if(!$projectArea->getProjectInverter()){
                $projectArea->setProjectInverter($this);
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
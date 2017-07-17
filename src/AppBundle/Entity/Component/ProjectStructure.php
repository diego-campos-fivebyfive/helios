<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStructure
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_structure")
 * @ORM\Entity
 */
class ProjectStructure implements ProjectStructureInterface
{
    use ProjectElementTrait;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectStructures")
     */
    private $project;

    /**
     * @var StructureInterface
     *
     * @ORM\ManyToOne(targetEntity="Structure")
     */
    private $structure;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
    }

    /**
     * @inheritDoc
     */
    public function setStructure(StructureInterface $structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectStructure($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
    }
}
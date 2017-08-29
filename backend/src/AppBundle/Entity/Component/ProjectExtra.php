<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectItem
 *
 * @ORM\Table(name="app_project_extra")
 * @ORM\Entity()
 */
class ProjectExtra implements ProjectExtraInterface
{
    use ProjectElementTrait;

    /**
     * @var ExtraInterface
     *
     * @ORM\ManyToOne(targetEntity="Extra")
     */
    private $extra;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectExtras")
     */
    private $project;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->unitCostPrice = 0;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function isService()
    {
        return $this->extra ? $this->extra->isService() : false;
    }

    /**
     * @inheritDoc
     */
    public function isProduct()
    {
        return $this->extra ? $this->extra->isProduct() : false;
    }

    /**
     * @inheritDoc
     */
    public function setExtra(ExtraInterface $extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectExtra($this);

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
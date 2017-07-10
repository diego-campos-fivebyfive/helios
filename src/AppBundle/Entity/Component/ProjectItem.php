<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectItem
 *
 * @ORM\Table(name="app_project_item")
 * @ORM\Entity()
 */
class ProjectItem implements ProjectItemInterface
{
    use ProjectElementTrait;

    /**
     * @var ItemInterface
     *
     * @ORM\ManyToOne(targetEntity="Item")
     */
    private $item;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectItems")
     */
    private $project;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity = 1;
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
        return $this->item->isService();
    }

    /**
     * @inheritDoc
     */
    public function isProduct()
    {
        return $this->item->isProduct();
    }

    /**
     * @inheritDoc
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
        $this->unitCostPrice = $item->getCostPrice();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectItem($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
    }

    /*/**
     * @inheritDoc
     *
    public function getUnitCostPrice()
    {
        return $this->item->getCostPrice();
    }

    /**
     * @inheritDoc
     *
    public function getTotalCostPrice()
    {
        return $this->quantity * $this->getUnitCostPrice();
    }*/
}
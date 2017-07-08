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
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

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
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;

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
}
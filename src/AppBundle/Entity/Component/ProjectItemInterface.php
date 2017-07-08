<?php

namespace AppBundle\Entity\Component;

interface ProjectItemInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $quantity
     * @return ProjectItemInterface
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param ItemInterface $item
     * @return ProjectItemInterface
     */
    public function setItem(ItemInterface $item);

    /**
     * @return ItemInterface
     */
    public function getItem();

    /**
     * @param ProjectInterface $project
     * @return ProjectItemInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();
}
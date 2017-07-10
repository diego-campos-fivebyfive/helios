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
     * @return bool
     */
    public function isService();

    /**
     * @return bool
     */
    public function isProduct();

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

    /**
     * @return float
     */
    public function getUnitCostPrice();

    /**
     * @param $unitSalePrice
     * @return ProjectItemInterface
     */
    public function setUnitSalePrice($unitSalePrice);

    /**
     * @return float
     */
    public function getUnitSalePrice();

    /**
     * @return float
     */
    public function getTotalSalePrice();

    /**
     * @return float
     */
    public function getTotalCostPrice();
}
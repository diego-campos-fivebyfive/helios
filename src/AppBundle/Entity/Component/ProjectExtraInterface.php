<?php

namespace AppBundle\Entity\Component;

interface ProjectExtraInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $quantity
     * @return ProjectExtraInterface
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
     * @param ExtraInterface $extra
     * @return ProjectExtraInterface
     */
    public function setExtra(ExtraInterface $extra);

    /**
     * @return ExtraInterface
     */
    public function getExtra();

    /**
     * @param ProjectInterface $project
     * @return ProjectExtraInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @param $unitCostPrice
     * @return float
     */
    public function setUnitCostPrice($unitCostPrice);

    /**
     * @return float
     */
    public function getUnitCostPrice();

    /**
     * @param $unitSalePrice
     * @return ProjectExtraInterface
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
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

/**
 * Interface ProjectElementInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectElementInterface
{
    /**
     * @param ProjectInterface $project
     * @return ProjectElementInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @param $quantity
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param object $markup
     * @return ProjectElementInterface
     */
    public function setMarkup($markup);

    /**
     * @return object
     */
    public function getMarkup();

    /**
     * @param $unitCostPrice
     * @return $this
     */
    public function setUnitCostPrice($unitCostPrice);

    /**
     * @return float
     */
    public function getUnitCostPrice();

    /**
     * @param $unitSalePrice
     * @return $this
     */
    public function setUnitSalePrice($unitSalePrice);

    /**
     * @return float
     */
    public function getUnitSalePrice();

    /**
     * @return float
     */
    public function getTotalCostPrice();

    /**
     * @return float
     */
    public function getTotalSalePrice();
}
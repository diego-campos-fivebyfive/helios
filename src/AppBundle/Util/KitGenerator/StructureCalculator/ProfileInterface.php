<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Interface ProfileInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProfileInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $size
     * @return ProfileInterface
     */
    public function setSize($size);

    /**
     * @return float
     */
    public function getSize();

    /**
     * @param $quantity
     * @return ProfileInterface
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();
}
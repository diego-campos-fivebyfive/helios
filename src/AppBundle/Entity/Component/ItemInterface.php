<?php

namespace AppBundle\Entity\Component;


interface ItemInterface
{
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
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $pricingBy
     * @return mixed
     */
    public function setPricingBy($pricingBy);

    /**
     * @return mixed
     */
    public function getPricingBy();

    /**
     * @param $costPrice
     * @return mixed
     */
    public function setCostPrice($costPrice);

    /**
     * @return mixed
     */
    public function getCostPrice();
}
<?php

namespace AppBundle\Entity\Order;

/**
 * Interface ElementInterface
 */
interface ElementInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $code
     * @return ElementInterface
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param $description
     * @return ElementInterface
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $quantity
     * @return ElementInterface
     */
    public function setQuantity($quantity);

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param $unitPrice
     * @return ElementInterface
     */
    public function setUnitPrice($unitPrice);

    /**
     * @return mixed
     */
    public function getUnitPrice();

    /**
     * @return mixed
     */
    public function getTotal();

    /**
     * @param OrderInterface $order
     * @return ElementInterface
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return mixed
     */
    public function getOrder();
}
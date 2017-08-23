<?php

namespace AppBundle\Entity\Order;

/**
 * Interface ElementInterface
 */
interface ElementInterface
{
    const TAG_MODULE = 'module';
    const TAG_INVERTER = 'inverter';
    const TAG_STRUCTURE = 'structure';
    const TAG_STRING_BOX = 'string_box';
    const TAG_VARIETY = 'variety';

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
     * @param $tag
     * @return ElementInterface
     */
    public function setTag($tag);

    /**
     * @return string
     */
    public function getTag();

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
     * @param array $metadata
     * @return ElementInterface
     */
    public function setMetadata(array $metadata = []);

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getMetadata($key = null, $default = null);

    /**
     * @param OrderInterface $order
     * @return ElementInterface
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @param \DateTime $created_at
     * @return ElementInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updated_at
     * @return ElementInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
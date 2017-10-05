<?php

namespace AppBundle\Entity\Order;

/**
 * Interface ElementInterface
 */
interface ElementInterface
{
    const FAMILY_MODULE = 'module';
    const FAMILY_INVERTER = 'inverter';
    const FAMILY_STRUCTURE = 'structure';
    const FAMILY_STRING_BOX = 'string_box';
    const FAMILY_VARIETY = 'variety';

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
     * @param $family
     * @return ElementInterface
     */
    public function setFamily($family);

    /**
     * @return string
     */
    public function getFamily();

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
     * @return mixed
     */
    public function getMarkup();

    /**
     * @param $markup
     * @return mixed
     */
    public function setMarkup($markup);

    /**
     * @return mixed
     */
    public function getCmv();

    /**
     * @param $cmv
     * @return mixed
     */
    public function setCmv($cmv);

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

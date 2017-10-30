<?php

namespace AppBundle\Entity\Stock;

interface TransactionInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param ProductInterface $product
     * @return TransactionInterface
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param $description
     * @return TransactionInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $amount
     * @return TransactionInterface
     */
    public function setAmount($amount);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}

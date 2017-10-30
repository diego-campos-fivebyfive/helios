<?php

namespace AppBundle\Entity\Stock;

interface ProductInterface
{
    /**
     * @param $id
     * @return string
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param $code
     * @return ProductInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $description
     * @return ProductInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getStock();

    /**
     * @param TransactionInterface $transaction
     * @return ProductInterface
     */
    public function addTransaction(TransactionInterface $transaction);

    /**
     * @param TransactionInterface $transaction
     * @return ProductInterface
     */
    public function removeTransaction(TransactionInterface $transaction);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransactions();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}

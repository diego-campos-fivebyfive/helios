<?php

namespace AppBundle\Entity;

interface SignatureInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param BusinessInterface $account
     * @return SignatureInterface
     */
    public function setAccount(BusinessInterface $account);

    /**
     * @return BusinessInterface
     */
    public function getAccount();

    /**
     * @param $subscriptionId
     * @return SignatureInterface
     */
    public function setSubscriptionId($subscriptionId);

    /**
     * @return mixed
     */
    public function getSubscriptionId();

    /**
     * @param $billId
     * @return SignatureInterface
     */
    public function setBillId($billId);

    /**
     * @return int
     */
    public function getBillId();

    /**
     * @param $content
     * @return SignatureInterface
     */
    public function setContent($content);

    /**
     * @return string[json]
     */
    public function getContent();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
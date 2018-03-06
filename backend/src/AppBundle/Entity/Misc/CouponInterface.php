<?php

namespace AppBundle\Entity\Misc;


use AppBundle\Entity\AccountInterface;

interface CouponInterface
{
    const SOURCE_CODE = 1;
    const SOURCE_RANKING = 2;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $code
     * @return CouponInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return CouponInterface
     */
    public function setName($name);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param $amount
     * @return CouponInterface
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param $target
     * @return CouponInterface
     */
    public function setTarget($target);

    /**
     * @return \DateTime
     */
    public function getAppliedAt();

    /**
     * @param $appliedAt
     * @return CouponInterface
     */
    public function setAppliedAt($appliedAt);

    /**
     * @return AccountInterface
     */
    public function getAccount();

    /**
     * @param $account
     * @return CouponInterface
     */
    public function setAccount($account);

    /**
     * @return boolean
     */
    public function isApplied();

    /**
     * @return int
     */
    public function getAppliedBy();

    /**
     * @param $appliedBy
     * @return CouponInterface
     */
    public function setAppliedBy($appliedBy);

    /**
     * @return array
     */
    public function getSources();
}

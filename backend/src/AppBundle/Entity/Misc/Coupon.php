<?php

namespace AppBundle\Entity\Misc;

use AppBundle\Entity\AccountInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Coupon
 *
 * @ORM\Table(name="app_coupon")
 * @ORM\Entity
 */
class Coupon implements CouponInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", nullable=true)
     */
    private $target;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="applied_at", type="datetime", nullable=true)
     */
    private $appliedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="applied_by", type="integer")
     */
    private $appliedBy;

    /**
     * @var AccountInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @inheritdoc
     */
    public function setTarget($target)
    {
        $this->target = $target;

        $this->appliedAt = !is_null($target) ? new \DateTime() : null;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAppliedAt()
    {
        return $this->appliedAt;
    }

    /**
     * @inheritdoc
     */
    public function setAppliedAt($appliedAt)
    {
        $this->appliedAt = $appliedAt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritdoc
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isApplied()
    {
        return !is_null($this->target);
    }

    /**
     * @inheritdoc
     */
    public function getAppliedBy()
    {
        return $this->appliedBy;
    }

    /**
     * @inheritdoc
     */
    public function setAppliedBy($appliedBy)
    {
        $this->appliedBy = $appliedBy;

        return $this;
    }

    /**
     * @return array
     */
    public function getSources()
    {
        return [
            self::SOURCE_CODE,
            self::SOURCE_RANKING
        ];
    }
}


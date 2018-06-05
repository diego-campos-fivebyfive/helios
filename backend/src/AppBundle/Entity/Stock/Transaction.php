<?php

namespace AppBundle\Entity\Stock;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Transaction
 *
 * @ORM\Table(name="app_stock_transaction")
 * @ORM\Entity
 */
class Transaction
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * TODO: Remove nullable definition after normalizations
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $family;

    /**
     * TODO: Remove nullable definition after normalizations
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $identity;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * TODO: Remove this property/methods after normalizations
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $productId;

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param string $family
     * @return Transaction
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param int $identity
     * @return Transaction
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function setProduct($product)
    {
        $this->productId = $product;

        return $this;
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function getProduct()
    {
        return $this->productId;
    }
}

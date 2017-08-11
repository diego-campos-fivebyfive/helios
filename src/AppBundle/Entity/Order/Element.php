<?php

namespace AppBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="app_order_element")
 * @ORM\Entity
 */
class Element implements ElementInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $unitPrice;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="elements")
     */
    private $order;

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
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
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        return $this->unitPrice * $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;

        $order->addElement($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return $this->order;
    }
}
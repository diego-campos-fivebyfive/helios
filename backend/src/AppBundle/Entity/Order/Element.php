<?php

namespace AppBundle\Entity\Order;

use AppBundle\Entity\Pricing\RangeInterface;
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $family;

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
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var float
     *
     * @ORM\Column(name="markup", type="float")
     */
    private $markup;

    /**
     * @var float
     *
     * @ORM\Column(name="cmv", type="float")
     */
    private $cmv;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $tax;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="elements")
     */
    private $order;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->metadata = [];
        $this->unitPrice = 0;
        $this->markup = 0;
        $this->cmv = 0;
        $this->tax = RangeInterface::DEFAULT_TAX;
        $this->discount = 0;
    }

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
     @inheritDoc
    */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     @inheritDoc
    */
    public function getFamily()
    {
        return $this->family;
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
    public function setMetadata(array $metadata = [])
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null, $default = null)
    {
        if($key){
            return array_key_exists($key, $this->metadata) ? $this->metadata[$key] : $default;
        }

        return $this->metadata;
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

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return $this->markup*100;
    }

    /**
     * @inheritDoc
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        $this->calculatePrice();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCmv()
    {
        return $this->cmv;
    }

    /**
     * @inheritDoc
     */
    public function setCmv($cmv)
    {
        $this->cmv = $cmv;

        $this->calculatePrice();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        $this->calculatePrice();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCmv()
    {
        return $this->cmv * $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function isFamily($family)
    {
        return $family === $this->family;
    }

    /**
     * @inheritDoc
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        $this->calculatePrice();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    private function calculatePrice()
    {
        $this->unitPrice = $this->cmv * (1 + $this->markup - $this->discount) / (1 - $this->tax);
    }


}

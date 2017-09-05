<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MetadataTrait;
use AppBundle\Entity\Pricing\InsurableTrait;
use AppBundle\Service\Pricing\InsurableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Order
 *
 * @ORM\Table(name="app_order")
 * @ORM\Entity
 */
class Order implements OrderInterface, InsurableInterface
{
    use MetadataTrait;
    use InsurableTrait;
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
     * @var integer
     *
     * @ORM\Column(name="isquik_id", type="integer", nullable=true)
     */
    private $isquikId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $power;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $shippingRules;

    /**
     * @var AccountInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="childrens")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Element", mappedBy="order", cascade={"persist", "remove"})
     */
    private $elements;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Order", mappedBy="parent", cascade={"persist", "remove"})
     */
    private $childrens;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->childrens = new ArrayCollection();
        $this->shippingRules = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setIsquikId($isquikId)
    {
        $this->isquikId = $isquikId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsquikId()
    {
        return $this->isquikId;
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
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @inheritDoc
     */
    public function setShippingRules(array $shippingRules)
    {
        $this->shippingRules = $shippingRules;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShippingRules()
    {
        return $this->shippingRules;
    }

    /**
     * @inheritDoc
     */
    public function getShipping()
    {
        return  is_array($this->shippingRules) && array_key_exists('shipping', $this->shippingRules)
            ? $this->shippingRules['shipping'] : 0 ;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        $total = 0;
        $sources = $this->isBudget() ? $this->childrens : $this->elements;
        foreach ($sources as $element){
            $total += $element->getTotal();
        }

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function getInsuranceQuota()
    {
        return $this->getTotal();
    }

    /**
     * @inheritDoc
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function setParent(OrderInterface $parent)
    {
        $this->parent = $parent;

        $parent->addChildren($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function addElement(ElementInterface $element)
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);

            if (!$element->getOrder()) $element->setOrder($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeElement(ElementInterface $element)
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function addChildren(OrderInterface $children)
    {
        if($children->isBudget() || $this->parent)
            throw new \InvalidArgumentException('You can not add a budget to another');

        if(!$this->childrens->contains($children)){
            $this->childrens->add($children);
            if(!$children->getParent()) $children->setParent($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeChildren(OrderInterface $children)
    {
        if($this->childrens->contains($children)){
            $this->childrens->removeElement($children);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @inheritDoc
     */
    public function isBudget()
    {
        return $this->childrens->count() > 0;
    }
}


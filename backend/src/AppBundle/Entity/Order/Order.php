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

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MetadataTrait;
use AppBundle\Entity\Pricing\InsurableTrait;
use AppBundle\Service\Pricing\InsurableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $reference;

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
     * @var DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $cnpj;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ie;

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
        $this->status = self::STATUS_BUILDING;
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
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getReference()
    {
        return $this->reference;
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
        if ($this->isBudget()) {
            $power = $this->power;
        } else {
            $power = $this->power + $this->getSubPower();
        }

        return $power;
    }

    public function getSubPower()
    {
        $power = 0;
        foreach ($this->childrens as $children)
        {
            $power += $children['power'];
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @inheritDoc
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @inheritDoc
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @inheritDoc
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @inheritDoc
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @inheritDoc
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @inheritDoc
     */
    public function setIe($ie)
    {
        $this->ie = $ie;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIe()
    {
        return $this->ie;
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
    public function getSubTotal()
    {
        $total = 0;
        $sources = $this->isBudget() ? $this->childrens : $this->elements;
        foreach ($sources as $element){
            $total += $element instanceof Element ? $element->getTotal() : $element->getSubTotal();
        }

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        $insurance = $this->isBudget() ? $this->getTotalInsurance() : $this->getInsurance();

        $total = $this->getSubTotal() + $this->getShipping() + $insurance;

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function getTotalInsurance()
    {
        $total = 0;
        foreach ($this->childrens as $children) {
            $total += $children->getInsurance();
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
    public function setPaymentMethod($paymentMethod)
    {
        $data = json_decode($paymentMethod, true);

        $this->addMetadata('payment_method', $data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod($format =  'json')
    {
        $data = $this->metadata['payment_method'];

        return  'json' == $format ? json_encode($data) : $data ;
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


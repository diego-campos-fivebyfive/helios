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

use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Pricing\RangeInterface;
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
     * @ORM\Column(name="status", type="smallint", nullable=true)
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
    private $proforma;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $filePayment;

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
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastname;

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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $level;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $shippingRules;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryAddress;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveryAt;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $deadline;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $source;

    /**
     * @var AccountInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @var MemberInterface|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    protected $agent;

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
        $this->source = self::SOURCE_ACCOUNT;
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
        return $this->reference ?: $this->id;
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
    public static function getStatusNames()
    {
        return [
            self::STATUS_BUILDING => 'building',
            self::STATUS_PENDING => 'pending',
            self::STATUS_VALIDATED => 'validated',
            self::STATUS_APPROVED => 'approved',
            self::STATUS_REJECTED => 'rejected',
            self::STATUS_DONE => 'done'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStatusName()
    {
        $statusNames = self::getStatusNames();

        if(is_null($this->status)) $this->status = self::STATUS_BUILDING;

        return $statusNames[$this->status];
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
    public function setProforma($proforma)
    {
        $this->proforma = $proforma;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProforma()
    {
        return $this->proforma;
    }

    /**
     * @inheritDoc
     */
    public function hasProforma()
    {
        return strlen($this->proforma);
    }

    /**
     * @inheritDoc
     */
    public function setFilePayment($filePayment)
    {
        $this->filePayment = $filePayment;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFilePayment()
    {
        return $this->filePayment;
    }

    /**
     * @inheritDoc
     */
    public function hasFilePayment()
    {
        return strlen($this->filePayment);
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
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        return $this->lastname;
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
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryAt(\DateTime $deliveryAt)
    {
        $this->deliveryAt = $deliveryAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryAt()
    {
        return $this->deliveryAt;
    }

    /**
     * @inheritDoc
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeadline()
    {
        return $this->deadline;
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

        foreach ($data['quotas'] as $key=>$quota){
            $percent = (float)$quota['percent']/100;
            $data['quotas'][$key]['value'] = $percent * $this->getTotal();
            $date = $quota['days'];
            $data['quotas'][$key]['date'] = (new \DateTime('+'.$date.'day'))->format('Y-m-d H:i:s');
        }

        $this->addMetadata('payment_method', $data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod($format =  'json')
    {
        $data = $this->getMetadata('payment_method');

        return  'json' == $format ? json_encode($data) : $data ;
    }

    /**
     * @inheritDoc
     */
    public function setAccount(AccountInterface $account)
    {
        $this->account = $account;

        if(null != $agent = $account->getAgent()){
            $this->setAgent($agent);
        }

        $this->level = $account->getLevel();

        $this->refreshCustomer();

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
    public function setAgent(MemberInterface $agent)
    {
        if(!$agent->isPlatformUser()){
            throw new \InvalidArgumentException('Invalid user role');
        }

        $this->agent = $agent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAgent()
    {
        return $this->agent;
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
        if($children->isBudget())
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
        return !$this->childrens->isEmpty() || !$this->parent;
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @inheritDoc
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * @inheritDoc
     */
    public function isBuilding()
    {
        return self::STATUS_BUILDING == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isPending()
    {
        return self::STATUS_PENDING == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isValidated()
    {
        return self::STATUS_VALIDATED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isApproved()
    {
        return self::STATUS_APPROVED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isRejected()
    {
        return self::STATUS_REJECTED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isDone()
    {
        return self::STATUS_DONE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCmv()
    {
        $totalCmv = 0;

        if ($this->isBudget()) {
            foreach ($this->childrens as $children) {
                $totalCmv += $children->getTotalCmv();
            }
        } else {
            foreach ($this->getElements() as $element) {
                $totalCmv += $element->getTotalCmv();
            }
        }

        return $totalCmv;

    }

    /**
     * @inheritDoc
     */
    public function getTotalTaxes()
    {
        return $this->getSubTotal() * RangeInterface::DEFAULT_TAX;
    }

    /**
     * @inheritDoc
     */
    public function getMargin()
    {
        return $this->getSubTotal() - $this->getTotalCmv() - $this->getTotalTaxes();
    }


    /**
     * Refresh customer data by reference account
     */
    private function refreshCustomer()
    {
        if($this->account instanceof AccountInterface) {
            $this->customer = $this->account->getFirstname();
            $this->cnpj = $this->account->getDocument();
            $this->ie = $this->account->getExtraDocument();
            $this->postcode = $this->account->getPostcode();
            $this->state = $this->account->getState();
            $this->city = $this->account->getCity();
            $this->address = $this->account->getStreet();

            if(null != $owner = $this->account->getOwner()) {
                $this->contact = $owner->getName();
                $this->email = $owner->getEmail();
                $this->phone = $owner->getPhone();
            }
        }
    }
}


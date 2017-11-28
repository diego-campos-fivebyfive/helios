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
use AppBundle\Entity\Pricing\MemorialInterface;
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
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="status_at", type="datetime", nullable=true)
     */
    private $statusAt;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $power;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", nullable=true)
     */
    private $invoiceNumber;

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
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $shipping;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_postcode", type="string", length=25, nullable=true)
     */
    private $deliveryPostcode;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_state", type="string", length=50, nullable=true)
     */
    private $delireryState;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_city", type="string", length=100, nullable=true)
     */
    private $deliveryCity;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_district", type="string", length=100, nullable=true)
     */
    private $deliveryDistrict;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_street", type="string", length=100, nullable=true)
     */
    private $deliveryStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_number", type="string", length=50, nullable=true)
     */
    private $deliveryNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_complement", type="string", length=100, nullable=true)
     */
    private $deliveryComplement;

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
     * @var int
     *
     * @ORM\Column(name="delivery_delay", type="smallint", nullable=true)
     */
    private $deliveryDelay;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $billingDirect;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingFirstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingLastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingContact;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingCnpj;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingIe;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingPhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingPostcode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingState;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingDistrict;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingStreet;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billingComplement;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $fileExtract;

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
     * @ORM\OneToMany(targetEntity="Message", mappedBy="order", cascade={"persist", "remove"})
     */
    private $messages;

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
        $this->messages = new ArrayCollection();
        $this->shippingRules = [];
        $this->setStatus(self::STATUS_BUILDING);
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
     * @inheritDoc
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        $this->statusAt = new \DateTime();

        $this->calculatePaymentMethod();

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
     * @return DateTime
     */
    public function getStatusAt()
    {
        return $this->statusAt;
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
            self::STATUS_DONE => 'confirmed',
            self::STATUS_INSERTED => 'inserted',
            self::STATUS_BILLED => 'billed',
            self::STATUS_AVAILABLE => 'available',
            self::STATUS_COLLECTED => 'collected',
            self::STATUS_DELIVERED => 'delivered'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_BUILDING => self::STATUS_BUILDING,
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_VALIDATED => self::STATUS_VALIDATED,
            self::STATUS_APPROVED => self::STATUS_APPROVED,
            self::STATUS_REJECTED => self::STATUS_REJECTED,
            self::STATUS_DONE => self::STATUS_DONE,
            self::STATUS_INSERTED => self::STATUS_INSERTED,
            self::STATUS_BILLED => self::STATUS_BILLED,
            self::STATUS_AVAILABLE => self::STATUS_AVAILABLE,
            self::STATUS_COLLECTED => self::STATUS_COLLECTED,
            self::STATUS_DELIVERED => self::STATUS_DELIVERED
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
    public function setDiscount($discount)
    {
        $this->discount = $discount;

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
        if ($this->isMaster()) {
            $power = 0;
            foreach ($this->childrens as $children) {
                $power += $children->getPower();
            }
        } else {
            $power = $this->power;
        }

        return $power;
    }

    /**
     * @deprecated
     */
    public function getSubPower()
    {
        return $this->getPower();
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

        $this->setShipping($this->getShipping());

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
    public function setShipping($shipping)
    {
        $this->shipping = round($shipping, 2);

        return $this;
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
        $address = '';
        if ($this->getDeliveryStreet())
            $address .= $this->getDeliveryStreet() . ',  ';
        if ($this->getDeliveryNumber())
            $address .= $this->getDeliveryNumber() . ' - ';
        if ($this->getDeliveryDistrict())
            $address .= $this->getDeliveryDistrict() . ', ';
        if ($this->getDeliveryCity())
            $address .= $this->getDeliveryCity() . ' - ';
        if ($this->getDeliveryState())
            $address .= $this->getDeliveryState() . ', ';
        if ($this->getDeliveryPostcode())
            $address .= $this->getDeliveryPostcode();
        if ($this->getDeliveryComplement())
            $address .= ' - ' . $this->getDeliveryComplement();

        return $address;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryPostcode($deliveryPostcode)
    {
        $this->deliveryPostcode = $deliveryPostcode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryPostcode()
    {
        return $this->deliveryPostcode;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryState($deliveryState)
    {
        $this->delireryState = $deliveryState;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryState()
    {
        return $this->delireryState;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryCity($deliveryCity)
    {
        $this->deliveryCity = $deliveryCity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryCity()
    {
        return $this->deliveryCity;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryDistrict($deliveryDistrict)
    {
        $this->deliveryDistrict = $deliveryDistrict;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryDistrict()
    {
        return $this->deliveryDistrict;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryStreet($deliveryStreet)
    {
        $this->deliveryStreet = $deliveryStreet;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryStreet()
    {
        return $this->deliveryStreet;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryNumber($deliveryNumber)
    {
        $this->deliveryNumber = $deliveryNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryNumber()
    {
        return $this->deliveryNumber;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryComplement($deliveryComplement)
    {
        $this->deliveryComplement = $deliveryComplement;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryComplement()
    {
        return $this->deliveryComplement;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryAt(\DateTime $deliveryAt = null)
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
        $sources = $this->isMaster() ? $this->childrens : $this->elements;
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
        $total = $this->getTotalExcDiscount() - $this->getDiscount();

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function setTotal($total)
    {
        $this->total = round($total, 2);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalExcDiscount()
    {
        $insurance = $this->isMaster() ? $this->getTotalInsurance() : $this->getInsurance();

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
        return $this->getSubTotal();
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethod($paymentMethod)
    {
        $data = json_decode($paymentMethod, true);

        $this->addMetadata('payment_method', $data);

        $this->calculatePaymentMethod();

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
    public function isParent()
    {
        return $this->hasChildren();
    }

    /**
     * @inheritDoc
     */
    public function isMaster()
    {
        return $this->isParent();
    }

    /**
     * @inheritDoc
     */
    public function addElement(ElementInterface $element)
    {
        if($this->isMaster())
            throw new \InvalidArgumentException('Master order can not have elements');

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
     * @inheritdoc
     */
    public function getElements($family = null)
    {
        $elements = $this->elements;

        if($family) {
            $elements = $elements->filter(function (ElementInterface $element) use($family) {
                return $element->isFamily($family);
            });
        }

        return $elements;
    }

    /**
     * @inheritDoc
     */
    public function addMessage(MessageInterface $message)
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);

            if (!$message->getOrder()) $message->setOrder($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMessage(MessageInterface $message)
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessages()
    {
        return $this->messages;
    }


    /**
     * @inheritDoc
     */
    public function isChildren()
    {
        return $this->parent instanceof OrderInterface;
    }

    /**
     * @inheritDoc
     */
    public function hasChildren()
    {
        return !$this->childrens->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function addChildren(OrderInterface $children)
    {
        if($children->isParent() || $children === $this || $this->isChildren())
            throw new \InvalidArgumentException('The children order is parent');

        if(!$this->childrens->contains($children)){

            $this->childrens->add($children);

            if(!$children->getLevel()) {
                $children->setLevel($this->level);
            }

            if(!$children->getParent()){
                $children->setParent($this);
            }
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
     * @deprecated This is a insecure method. Can be removed.
     * @see Order::isMaster() method
     * @inheritDoc
     */
    public function isBudget()
    {
        return $this->isMaster();
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
    public function isPromotional()
    {
        return MemorialInterface::LEVEL_PROMOTIONAL == $this->level;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryDelay($deliveryDelay)
    {
        $this->deliveryDelay = $deliveryDelay;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryDelay()
    {
        return $this->deliveryDelay;
    }

    /**
     * @inheritDoc
     */
    public function setBillingDirect($billingDirect)
    {
        $this->billingDirect = $billingDirect;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isBillingDirect()
    {
        return $this->billingDirect;
    }

    /**
     * @inheritDoc
     */
    public function setBillingFirstname($billingFirstname)
    {
        $this->billingFirstname = $billingFirstname;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingFirstname()
    {
        return $this->billingFirstname;
    }

    /**
     * @inheritDoc
     */
    public function setBillingLastname($billingLastname)
    {
        $this->billingLastname = $billingLastname;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingLastname()
    {
        return $this->billingLastname;
    }

    /**
     * @inheritDoc
     */
    public function setBillingContact($billingContact)
    {
        $this->billingContact = $billingContact;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingContact()
    {
        return $this->billingContact;
    }

    /**
     * @inheritDoc
     */
    public function setBillingCnpj($billingCnpj)
    {
        $this->billingCnpj = $billingCnpj;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingCnpj()
    {
        return $this->billingCnpj;
    }

    /**
     * @inheritDoc
     */
    public function setBillingIe($billingIe)
    {
        $this->billingIe = $billingIe;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingIe()
    {
        return $this->billingIe;
    }

    /**
     * @inheritDoc
     */
    public function setBillingPhone($billingPhone)
    {
        $this->billingPhone = $billingPhone;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    /**
     * @inheritDoc
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * @inheritDoc
     */
    public function setBillingPostcode($billingPostcode)
    {
        $this->billingPostcode = $billingPostcode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    /**
     * @inheritDoc
     */
    public function setBillingState($billingState)
    {
        $this->billingState = $billingState;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingState()
    {
        return $this->billingState;
    }

    /**
     * @inheritDoc
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * @inheritDoc
     */
    public function setBillingDistrict($billingDistrict)
    {
        $this->billingDistrict = $billingDistrict;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingDistrict()
    {
        return $this->billingDistrict;
    }

    /**
     * @inheritDoc
     */
    public function setBillingStreet($billingStreet)
    {
        $this->billingStreet = $billingStreet;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingStreet()
    {
        return $this->billingStreet;
    }

    /**
     * @inheritDoc
     */
    public function setBillingNumber($billingNumber)
    {
        $this->billingNumber = $billingNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingNumber()
    {
        return $this->billingNumber;
    }

    /**
     * @inheritDoc
     */
    public function setBillingComplement($billingComplement)
    {
        $this->billingComplement = $billingComplement;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingComplement()
    {
        return $this->billingComplement;
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress()
    {
        return sprintf(
            '%s, %s - %s, %s-%s, %s (%s)',
            $this->billingStreet,
            $this->billingNumber,
            $this->billingDistrict,
            $this->billingCity,
            $this->billingState,
            $this->billingPostcode,
            $this->billingComplement
        );
    }

    /**
     * @inheritDoc
     */
    public function isFullyPromotional()
    {
        if (!$this->getChildrens()->count())
            return false;
        foreach ($this->getChildrens() as $order)
            if (!$order->isPromotional())
                return false;
        return true;
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
     * @inheritdoc
     */
    public function isInserted()
    {
        return self::STATUS_INSERTED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isAvailable()
    {
        return self::STATUS_AVAILABLE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isCollected()
    {
        return self::STATUS_COLLECTED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isBilled()
    {
        return self::STATUS_BILLED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isDelivered()
    {
        return self::STATUS_DELIVERED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCmv()
    {
        $totalCmv = 0;

        if ($this->isMaster()) {
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
            $this->firstname = $this->account->getFirstname();
            $this->lastname = $this->account->getLastname();
            $this->cnpj = $this->account->getDocument();
            $this->ie = $this->account->getExtraDocument();
            $this->postcode = $this->account->getPostcode();
            $this->state = $this->account->getState();
            $this->city = $this->account->getCity();
            $this->address = $this->account->getStreet();

            if(null != $owner = $this->account->getOwner()) {
                $this->contact = $owner->getFirstname();
                $this->email = $owner->getEmail();
                $this->phone = $owner->getPhone();
            }
        }
    }

    /**
     * @inheritdoc
     */
    private function calculatePaymentMethod()
    {
        $data = $this->getPaymentMethod('array');

        if ($this->status == self::STATUS_APPROVED) {
            $accumulation = 0;
            foreach ($data['quotas'] as $key => $quota) {
                $percent = (float)$quota['percent'] / 100;
                $data['quotas'][$key]['value'] = $percent * $this->getTotal();
                $data['quotas'][$key]['date'] = $this->statusAt->add(new \DateInterval('P'.($quota['days'] - $accumulation).'D'))->format('Y-m-d H:i:s');
                $accumulation = $quota['days'];
            }

            $this->statusAt = new \DateTime('now');
        }

        $this->addMetadata('payment_method', $data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFileExtract()
    {
        return $this->fileExtract;
    }

    /**
     * @inheritDoc
     */
    public function setFileExtract($fileExtract)
    {
        if(!$this->isMaster())
            throw new \InvalidArgumentException('Suborders do not store the generated CSV file name');

        $this->fileExtract = $fileExtract;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }


}


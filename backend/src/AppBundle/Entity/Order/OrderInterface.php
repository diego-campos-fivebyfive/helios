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
use AppBundle\Entity\MemberInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Interface OrderInterface
 * @package AppBundle\Entity\Order
 */
interface OrderInterface
{
    const STATUS_BUILDING = 0;      // Order opened, but not yet submitted
    const STATUS_PENDING = 1;       // Order submitted, but not yet validated by user sices
    const STATUS_VALIDATED = 2;     // Order validated, waiting for integrator user action
    const STATUS_APPROVED = 3;      // Order approved, awaiting payment
    const STATUS_REJECTED = 4;      // Order rejected, actions closed
    const STATUS_DONE = 5;          // Order done, payment confirmed
    const STATUS_INSERTED = 6;      // Order inserted on CRM (Protheus),
    const STATUS_AVAILABLE = 7;     // Product available for delivery collect
    const STATUS_COLLECTED = 8;     // Product collected for delivery
    const STATUS_BILLED = 9;        // Billed product
    const STATUS_DELIVERED = 10;    // Product delivered

    const SOURCE_ACCOUNT = 0;
    const SOURCE_PLATFORM = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $reference
     * @return OrderInterface
     */
    public function setReference($reference);

    /**
     * @return string
     */
    public function getReference();

    /**
     * @param $description
     * @return OrderInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $note
     * @return OrderInterface
     */
    public function setNote($note);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param $note
     * @return OrderInterface
     */
    public function setMessage($note);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param AccountInterface $account
     * @return OrderInterface
     */
    public function setAccount(AccountInterface $account);

    /**
     * @return AccountInterface
     */
    public function getAccount();

    /**
     * @param MemberInterface $agent
     * @return OrderInterface
     */
    public function setAgent(MemberInterface $agent);

    /**
     * @return MemberInterface|null
     */
    public function getAgent();

    /**
     * @param $status
     * @return int
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getPreviousStatus();

    /**
     * @return \DateTime
     */
    public function getStatusAt();

    /**
     * @param $discount
     * @return OrderInterface
     */
    public function setDiscount($discount);

    /**
     * @return float
     */
    public function getDiscount();

    /**
     * @return int
     */
    public static function getStatusNames();

    /**
     * @return int
     */
    public static function getStatusList();

    /**
     * @return string
     */
    public function getStatusName();

    /**
     * @return float
     */
    public function getSubTotal();

    /**
     * @return float
     */
    public function getTotal();

    /**
     * @param $total
     * @return OrderInterface
     */
    public function setTotal($total);

    /**
     * @return float
     */
    public function getTotalExcDiscount();

    /**
     * @return float
     */
    public function getTotalInsurance();

    /**
     * @param float $power
     * @return OrderInterface
     */
    public function setPower($power);

    /**
     * @return float
     */
    public function getPower();

    /**
     * @param ElementInterface $element
     * @return OrderInterface
     */
    public function addElement(ElementInterface $element);

    /**
     * @param ElementInterface $element
     * @return OrderInterface
     */
    public function removeElement(ElementInterface $element);

    /**
     * @param null $family
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getElements($family = null);

    /**
     * @param MessageInterface $message
     * @return OrderInterface
     */
    public function addMessage(MessageInterface $message);

    /**
     * @param MessageInterface $message
     * @return OrderInterface
     */
    public function removeMessage(MessageInterface $message);

    /**
     * @param null $order
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMessages();

    /**
     * @param OrderInterface $parent
     * @return OrderInterface
     */
    public function setParent(OrderInterface $parent);

    /**
     * @return OrderInterface|null
     */
    public function getParent();

    /**
     * @return bool
     */
    public function isParent();

    /**
     * @return bool
     */
    public function isMaster();

    /**
     * @return bool
     */
    public function isChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param OrderInterface $children
     * @return OrderInterface
     */
    public function addChildren(OrderInterface $children);

    /**
     * @param OrderInterface $children
     * @return OrderInterface
     */
    public function removeChildren(OrderInterface $children);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildrens();

    /**
     * @deprecated This method can be removed
     * @see Order::isMaster
     *
     * @return bool
     */
    public function isBudget();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param $sendAt
     * @return OrderInterface
     */
    public function setSendAt($sendAt);

    /**
     * @return \DateTime
     */
    public function getSendAt();

    /**
     * @param string $proforma
     * @return OrderInterface
     */
    public function setProforma($proforma);

    /**
     * @return string
     */
    public function getProforma();

    /**
     * @return bool
     */
    public function hasProforma();

    /**
     * @param $filePayment
     * @return OrderInterface
     */
    public function setFilePayment($filePayment);

    /**
     * @return string
     */
    public function getFilePayment();

    /**
     * @return float
     */
    public function getTotalCmv();

    /**
     * @return float
     */
    public function getTotalTaxes();

    /**
     * @return float
     */
    public function getMargin();

    /**
     * @return bool
     */
    public function hasFilePayment();

    /**
     * @param string $contact
     * @return OrderInterface
     */
    public function setContact($contact);

    /**
     * @return string
     */
    public function getContact();

    /**
     * @param string $email
     * @return OrderInterface
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $phone
     * @return OrderInterface
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $firstname
     * @return OrderInterface
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param string $lastname
     * @return OrderInterface
     */
    public function setLastname($lastname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param string $postcode
     * @return OrderInterface
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $address
     * @return OrderInterface
     */
    public function setAddress($address);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $city
     * @return OrderInterface
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $state
     * @return OrderInterface
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $cnpj
     * @return OrderInterface
     */
    public function setCnpj($cnpj);

    /**
     * @return string
     */
    public function getCnpj();

    /**
     * @param string $ie
     * @return OrderInterface
     */
    public function setIe($ie);

    /**
     * @return string
     */
    public function getIe();

    /**
     * @param array $shippingRules
     * @return OrderInterface
     */
    public function setShippingRules(array $shippingRules);

    /**
     * @return array
     */
    public function getShippingRules();

    /**
     * @return float
     */
    public function getShipping();

    /**
     * @param $shipping
     * @return OrderInterface
     */
    public function setShipping($shipping);

    /**
     * @param $deliveryAddress
     * @return OrderInterface
     */
    public function setDeliveryAddress($deliveryAddress);

    /**
     * @return string
     */
    public function getDeliveryAddress();

    /**
     * @param $deliveryPostcode
     * @return OrderInterface
     */
    public function setDeliveryPostcode($deliveryPostcode);

    /**
     * @return string
     */
    public function getDeliveryPostcode();

    /**
     * @param $deliveryState
     * @return OrderInterface
     */
    public function setDeliveryState($deliveryState);

    /**
     * @return string
     */
    public function getDeliveryState();

    /**
     * @param $deliveryCity
     * @return OrderInterface
     */
    public function setDeliveryCity($deliveryCity);

    /**
     * @return string
     */
    public function getDeliveryCity();

    /**
     * @param $deliveryDistrict
     * @return OrderInterface
     */
    public function setDeliveryDistrict($deliveryDistrict);

    /**
     * @return string
     */
    public function getDeliveryDistrict();

    /**
     * @param $deliveryStreet
     * @return OrderInterface
     */
    public function setDeliveryStreet($deliveryStreet);

    /**
     * @return string
     */
    public function getDeliveryStreet();

    /**
     * @param $deliveryNumber
     * @return OrderInterface
     */
    public function setDeliveryNumber($deliveryNumber);

    /**
     * @return string
     */
    public function getDeliveryNumber();

    /**
     * @param $deliveryComplement
     * @return OrderInterface
     */
    public function setDeliveryComplement($deliveryComplement);

    /**
     * @return string
     */
    public function getDeliveryComplement();

    /**
     * @param \DateTime $deliveryAt
     * @return OrderInterface
     */
    public function setDeliveryAt(\DateTime $deliveryAt);

    /**
     * @return \DateTime
     */
    public function getDeliveryAt();

    /**
     * @param $deadline
     * @return OrderInterface
     */
    public function setDeadline($deadline);

    /**
     * @return int
     */
    public function getDeadline();

    /**
     * @param $paymentMethod
     * @return OrderInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @param string $format
     * @return null|array|string
     */
    public function getPaymentMethod($format = 'json');

    /**
     * @param $source
     * @return OrderInterface
     */
    public function setSource($source);

    /**
     * @return int
     */
    public function getSource();

    /**
     * @return bool
     */
    public function isBuilding();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return bool
     */
    public function isValidated();

    /**
     * @return bool
     */
    public function isApproved();

    /**
     * @return bool
     */
    public function isRejected();

    /**
     * @return bool
     */
    public function isDone();

    /**
     * @return bool
     */
    public function isInserted();

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return bool
     */
    public function isCollected();

    /**
     * @return bool
     */
    public function isBilled();

    /**
     * @return bool
     */
    public function isDelivered();

    /**
     * @param $level
     * @return OrderInterface
     */
    public function setLevel($level);

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @return bool
     */
    public function isPromotional();

    /**
     * @return bool
     */
    public function isFullyPromotional();

    /**
     * @param $deliveryDelay
     * @return OrderInterface
     */
    public function setDeliveryDelay($deliveryDelay);

    /**
     * @return int
     */
    public function getDeliveryDelay();

    /**
     * @param $billingDirect
     * @return OrderInterface
     */
    public function setBillingDirect($billingDirect);

    /**
     * @return string
     */
    public function isBillingDirect();

    /**
     * @param $billingFirstname
     * @return OrderInterface
     */
    public function setBillingFirstname($billingFirstname);

    /**
     * @return string
     */
    public function getBillingFirstname();

    /**
     * @param $billingLastname
     * @return OrderInterface
     */
    public function setBillingLastname($billingLastname);

    /**
     * @return string
     */
    public function getBillingLastname();

    /**
     * @param $billingContact
     * @return OrderInterface
     */
    public function setBillingContact($billingContact);

    /**
     * @return string
     */
    public function getBillingContact();

    /**
     * @param $billingCnpj
     * @return OrderInterface
     */
    public function setBillingCnpj($billingCnpj);

    /**
     * @return string
     */
    public function getBillingCnpj();

    /**
     * @param $billingIe
     * @return OrderInterface
     */
    public function setBillingIe($billingIe);

    /**
     * @return string
     */
    public function getBillingIe();

    /**
     * @param $billingPhone
     * @return OrderInterface
     */
    public function setBillingPhone($billingPhone);

    /**
     * @return string
     */
    public function getBillingPhone();

    /**
     * @param $billingEmail
     * @return OrderInterface
     */
    public function setBillingEmail($billingEmail);

    /**
     * @return string
     */
    public function getBillingEmail();

    /**
     * @param $billingPostcode
     * @return OrderInterface
     */
    public function setBillingPostcode($billingPostcode);

    /**
     * @return string
     */
    public function getBillingPostcode();

    /**
     * @param $billingState
     * @return OrderInterface
     */
    public function setBillingState($billingState);

    /**
     * @return string
     */
    public function getBillingState();

    /**
     * @param $billingCity
     * @return OrderInterface
     */
    public function setBillingCity($billingCity);

    /**
     * @return string
     */
    public function getBillingCity();

    /**
     * @param $billingDistrict
     * @return OrderInterface
     */
    public function setBillingDistrict($billingDistrict);

    /**
     * @return string
     */
    public function getBillingDistrict();

    /**
     * @param $billingStreet
     * @return OrderInterface
     */
    public function setBillingStreet($billingStreet);

    /**
     * @return string
     */
    public function getBillingStreet();

    /**
     * @param $billingNumber
     * @return OrderInterface
     */
    public function setBillingNumber($billingNumber);

    /**
     * @return string
     */
    public function getBillingNumber();


    /**
     * @param $billingComplement
     * @return OrderInterface
     */
    public function setBillingComplement($billingComplement);

    /**
     * @return string
     */
    public function getBillingComplement();

    /**
     * @return string
     */
    public function getBillingAddress();

    /**
     * @return string
     */
    public function getFileExtract();

    /**
     * @param $fileExtract
     * @return OrderInterface
     */
    public function setFileExtract($fileExtract);

    /**
     * @param $invoiceNumber
     * @return OrderInterface
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * @param $expireDays
     * @return OrderInterface
     */
    public function setExpireDays($expireDays);

    /**
     * @return int
     */
    public function getExpireDays();

    /**
     * @param \DateTime $expireAt
     * @return OrderInterface
     */
    public function setExpireAt(\DateTime $expireAt);

    /**
     * @return \DateTime
     */
    public function getExpireAt();

    /**
     * @param string $expireNote
     * @return OrderInterface
     */
    public function setExpireNote($expireNote);

    /**
     * @return string
     */
    public function getExpireNote();
}

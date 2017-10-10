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
    const STATUS_BUILDING = 0;
    const STATUS_PENDING = 1;
    const STATUS_VALIDATED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_DONE = 5;

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
    public static function getStatusNames();

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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getElements();

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
     * @param $deliveryAddress
     * @return OrderInterface
     */
    public function setDeliveryAddress($deliveryAddress);

    /**
     * @return string
     */
    public function getDeliveryAddress();

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
     * @param $level
     * @return OrderInterface
     */
    public function setLevel($level);

    /**
     * @return string
     */
    public function getLevel();
}

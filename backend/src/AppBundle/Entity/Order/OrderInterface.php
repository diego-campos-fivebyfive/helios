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
use Doctrine\ORM\Mapping as ORM;

interface OrderInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $isquikId
     * @return OrderInterface
     */
    public function setIsquikId($isquikId);

    /**
     * @return integer
     */
    public function getIsquikId();

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
     * @param $account
     * @return OrderInterface
     */
    public function setAccount($account);

    /**
     * @return AccountInterface
     */
    public function getAccount();

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
     * @return float
     */
    public function getTotal();

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
     * @param array $shippingRules
     * @return ProjectInterface
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
}
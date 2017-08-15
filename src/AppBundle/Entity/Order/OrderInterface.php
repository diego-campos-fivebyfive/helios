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
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
<?php

namespace AppBundle\Entity\Order;

use AppBundle\Entity\Misc\AdditiveInterface;
use AppBundle\Entity\Misc\AdditiveRelationTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAdditive
 *
 * @ORM\Table(name="app_order_additive")
 * @ORM\Entity
 */
class OrderAdditive implements OrderAdditiveInterface
{
    use AdditiveRelationTrait;


    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderAdditives")
     */
    private $order;


    /**
     * @inheritDoc
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;

        $order->addOrderAdditive($this);

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
    public function getAdditiveQuota()
    {
        return $this->order->getSubTotal();
    }
}


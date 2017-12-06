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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderAdditives")
     */
    private $order;

    /**
     * @var AdditiveInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Misc\Additive")
     */
    private $additive;

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
    public function setAdditive(AdditiveInterface $additive)
    {
        $this->additive = $additive;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdditive()
    {
        return $this->additive;
    }

    /**
     * @inheritDoc
     */
    public function getAdditiveQuota()
    {
        return $this->order->getSubTotal();
    }
}


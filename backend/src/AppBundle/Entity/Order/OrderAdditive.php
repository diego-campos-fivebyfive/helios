<?php

namespace AppBundle\Entity\Misc;

use AppBundle\Entity\Order\OrderInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAdditive
 *
 * @ORM\Table(name="app_order_additive")
 * @ORM\Entity
 */
class OrderAdditive implements OrderAdditiveInterface
{
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order\Order", inversedBy="orderAdditives")
     */
    private $order;

    /**
     * @var AdditiveInterface
     *
     * @ORM\ManyToOne(targetEntity="Additive")
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
}


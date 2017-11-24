<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Manager\OrderManager;

class OrderManipulator
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * OrderManipulator constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function normalizeInfo(OrderInterface $order)
    {
        //dump($order);die;

        $order->setTotal($order->getTotal());

        if ($order->isMaster()) {
            $order->setPower($order->getPower());
            $order->setShipping($order->getShipping());
        }

        $this->manager->save($order);

        if (!$order->isMaster() && $order->getParent())
            self::normalizeInfo($order->getParent());

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param $next
     * @param UserInterface $user
     * @return bool
     */
    public function acceptStatus(OrderInterface $order, $next, UserInterface $user)
    {
        return StatusChecker::acceptStatus($order->getStatus(), $next, $user->getType(), $user->getRoles());
    }

    /**
     * @param OrderInterface $order
     */
    public static function checkPower(OrderInterface $order)
    {
        $power = 0;
        /** @var ElementInterface $element */
        foreach ($order->getElements() as $element) {
            if (ElementInterface::FAMILY_MODULE == $element->getFamily()) {
                $maxPower = $element->getMetadata('max_power', 0);
                if ($maxPower > 0) {
                    $power += ($maxPower / 1000) * $element->getQuantity();
                }
            }
        }

        $order
            ->setPower($power)
            ->setDescription(sprintf('Sistema de %skWp%s', $power, ($order->isPromotional() ? ' [promo]': '')))
        ;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public static function getCodes(OrderInterface $order)
    {
        return $order->getElements()->map(function (ElementInterface $element) {
            return $element->getCode();
        })->toArray();
    }
}

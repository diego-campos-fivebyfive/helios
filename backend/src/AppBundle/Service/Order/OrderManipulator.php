<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\UserInterface;

class OrderManipulator
{
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

        $order->setPower($power);
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

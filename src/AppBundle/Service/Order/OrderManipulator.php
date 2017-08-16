<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\OrderInterface;

class OrderManipulator
{
    /**
     * @param OrderInterface $order
     */
    public static function checkPower(OrderInterface $order)
    {
        $power = 0;
        /** @var ElementInterface $element */
        foreach ($order->getElements() as $element){
            if(ElementInterface::TAG_MODULE == $element->getTag()){
                $maxPower = $element->getMetadata('max_power', 0);
                if($maxPower > 0) {
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
        return $order->getElements()->map(function (ElementInterface $element){
            return $element->getCode();
        })->toArray();
    }
}
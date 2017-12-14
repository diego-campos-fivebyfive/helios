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
        ;

        self::updateDescription($order);
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

    public static function updateDescription(OrderInterface $order)
    {
        $power = $order->getPower();

        if ($order->isPromotional()) {
            $result = ' [promo]';
        }
        else if ($order->isFiname()) {
            if ($power >= 375) {
                $cod = '03453899';
            }
            else if ($power >= 75) {
                $cod = '03454175';
            }
            else {
                $cod = '03454168';
            }

            $result = " [finame - ${cod}]";
        }
        else {
            $result = '';
        }

        $description = sprintf('Sistema de %skWp%s', $power, $result);
        $order->setDescription($description);
    }
}

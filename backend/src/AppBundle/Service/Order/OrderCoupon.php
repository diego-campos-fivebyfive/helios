<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
use AppBundle\Manager\ParameterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderCoupon
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * OrderCoupon constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function generateOptions(Order $order)
    {
        $account = $order->getAccount();
        $accountRanking = $account->getRanking();

        /** @var ParameterManager $parameters */
        $parameters = $this->container->get('parameter_manager');
        $parameter = $parameters->findOrCreate('platform_settings')->getParameters();
        $step = $parameter['coupon_step_options'];

        if ($accountRanking < $step) {
            return [];
        }

        $maxDiscountPercent = $parameter['coupon_order_percent'] / 100;
        $maxOrderDiscount = $order->getTotal() * $maxDiscountPercent;
        $discountLimit = $maxOrderDiscount < $accountRanking ? $maxOrderDiscount : $accountRanking;

        $ranges = range($step, intval($discountLimit), $step);

        return $ranges;
    }
}

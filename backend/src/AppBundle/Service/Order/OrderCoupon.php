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

        $options = [];

        /** @var ParameterManager $parameters */
        $parameters = $this->container->get('parameter_manager');
        $parameter = $parameters->findOrCreate('platform_settings')->getParameters();
        $percent = $parameter['coupon_order_percent'] / 100;

        if ($account->getRanking() < $parameter['coupon_step_options']) {
            return $options;
        }

        $limit = $order->getTotal() * $percent;

        $stepLimit = $limit < $account->getRanking() ? $limit : $account->getRanking();

        $steps = floor($stepLimit / $parameter['coupon_step_options']);

        for ($i = 0; $i < $steps; $i++) {
            $options[] = $parameter['coupon_step_options'] * ($i + 1);
        }

        return $options;
    }
}

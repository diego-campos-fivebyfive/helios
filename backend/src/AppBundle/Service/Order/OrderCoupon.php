<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
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

        $params = [
            'percent' => 0.5,
            'step' => 500
        ];

        if ($account->getRanking() < $params['step']) {
            return $options;
        }

        $limit = $order->getTotal() * $params['percent'];

        $stepLimit = $limit < $account->getRanking() ? $limit : $account->getRanking();

        $steps = floor($stepLimit / $params['step']);

        for ($i = 0; $i < $steps; $i++) {
            $options[] = $params['step'] * ($i + 1);
        }

        return $options;
    }
}

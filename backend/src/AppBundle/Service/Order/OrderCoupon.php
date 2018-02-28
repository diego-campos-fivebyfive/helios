<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
use AppBundle\Manager\ParameterManager;
use AppBundle\Service\Business\RankingGenerator;
use AppBundle\Service\Coupon\Transformer;
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

    public function generateCoupon(Order $order, $amount)
    {
        $ranges = $this->generateOptions($order);

        if (!in_array($amount, $ranges)) {
            return null;
        }

        $account = $order->getAccount();

        /** @var Transformer $transformer */
        $transformer = $this->container->get('coupon_transformer');
        $coupon = $transformer->fromAccount($account, $amount);

        if (!$coupon) {
            return null;
        }

        $this->debitRanking($account,$coupon, $amount);

        return $coupon;
    }

    private function debitRanking($account, $coupon, $amount)
    {
        $debitAmount = - $amount;
        /** @var RankingGenerator $rankingGenerator */
        $rankingGenerator = $this->container->get('ranking_generator');
        $rankingGenerator->create($account, $coupon->getName(), $debitAmount);
    }
}

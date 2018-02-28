<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Order\Order;
use AppBundle\Manager\CouponManager;
use AppBundle\Manager\ParameterManager;
use Doctrine\Common\Util\ClassUtils;
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

    /**
     * @param Order $order
     * @param $coupon
     * @return bool
     */
    public function associateCoupon(Order $order, $coupon)
    {
        /** @var CouponManager $couponManager */
        $couponManager = $this->container->get('coupon_manager');

        if (!$coupon instanceof Coupon) {
            $coupon = $couponManager->findOneBy(['code' => $coupon]);
        }

        if (!$coupon || $order->getAccount() !== $coupon->getAccount()) {
            return false;
        }

        $coupon->setTarget($this->getTarget($order));
        $couponManager->save($coupon);

        $order->setCoupon($coupon);
        $this->container->get('order_manager')->save($order);

        return true;
    }

    /**
     * @param Order $order
     * @param $amount
     * @return mixed|null|object
     */
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

        $this->associateCoupon($order, $coupon);

        $this->debitRanking($account, $coupon, $amount);

        return $coupon;
    }

    /**
     * @param Order $order
     * @return string
     */
    private function getTarget(Order $order)
    {
        return sprintf('%s::%s', ClassUtils::getClass($order), $order->getId());
    }

    /**
     * @param $account
     * @param $coupon
     * @param $amount
     */
    private function debitRanking($account, $coupon, $amount)
    {
        $debitAmount = - $amount;
        /** @var RankingGenerator $rankingGenerator */
        $rankingGenerator = $this->container->get('ranking_generator');
        $rankingGenerator->create($account, $coupon->getName(), $debitAmount);
    }
}

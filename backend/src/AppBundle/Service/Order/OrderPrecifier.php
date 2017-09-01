<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Pricing\RangeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderPrecifier
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param OrderInterface $order
     */
    public function precify(OrderInterface $order)
    {
        OrderManipulator::checkPower($order);

        $power = $this->checkPower($order);
        $level = $this->checkLevel($order);
        $codes = $this->checkCodes($order);
        $memorial = $this->checkMemorial();

        /** @var \AppBundle\Service\Pricing\RangeLoader $loader */
        $loader = $this->container->get('range_loader');

        $ranges = $loader->load($memorial, $power, $level, $codes);

        foreach($order->getElements() as $element){
            $code = $element->getCode();
            $range = $ranges[$code];

            if($range instanceof RangeInterface){
                $price = (float) $range->getPrice();
                $element->setUnitPrice($price);
            }
        }

        $order->addMetadata('memorial', $memorial->toArray());

        $this->container->get('order_manager')->save($order);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function checkCodes(OrderInterface $order)
    {
        $codes = OrderManipulator::getCodes($order);

        if(empty($codes)){
            throw new \InvalidArgumentException('The order is empty');
        }

        return $codes;
    }

    /**
     * @param OrderInterface $order
     * @return float
     */
    private function checkPower(OrderInterface $order)
    {
        $power = $order->getPower();

        if($power <= 0){
            throw new \InvalidArgumentException('Undefined order power');
        }

        return $power;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function checkLevel(OrderInterface $order)
    {
        $account = $order->getAccount();

        $error = 'Order account is undefined';

        if($account instanceof AccountInterface){

            if(null != $level = $account->getLevel()){
                return $level;
            }

            $error = 'Invalid account level';
        }

        throw new \InvalidArgumentException($error);
    }

    /**
     * @return \AppBundle\Entity\Pricing\MemorialInterface
     */
    private function checkMemorial()
    {
        $memorial = $this->container->get('memorial_loader')->load();

        if(!$memorial){
            throw new \InvalidArgumentException('Memorial not loaded');
        }

        return $memorial;
    }
}
<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Pricing\RangeInterface;
use Proxies\__CG__\AppBundle\Entity\Pricing\Range;
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
        $level = $order->getLevel();
        $codes = $this->checkCodes($order);
        $memorial = $this->checkMemorial();
        $powerRange = $order->getMetadata('power_range');
        $rangeIsChanged = true;

        if ($powerRange){
            $rangeIsChanged = ($power < $powerRange[0] || $power > $powerRange[1]);
        }

        /** @var \AppBundle\Service\Pricing\RangeLoader $loader */
        $loader = $this->container->get('range_loader');

        $ranges = $loader->load($memorial, $power, $level, $codes);

        foreach ($order->getElements() as $element) {

            $code = $element->getCode();

            if (array_key_exists($code, $ranges)) {

                $range = $ranges[$code];
                if ($range instanceof RangeInterface) {
                    $cmv = (float)$range->getCostPrice();
                    $markup = (float)$range->getMarkup();
                    $element->setCmv($cmv);
                    if ($rangeIsChanged || !$element->getMarkup()) {
                        $element->setMarkup($markup);
                    }
                    $element->setTax(Range::DEFAULT_TAX);

                    $order->addMetadata('power_range', [$range->getInitialPower(), $range->getFinalPower()]);
                }
            }
        }

        $order->addMetadata('memorial', $memorial->toArray());

        $order->setTotal($order->getTotal());

        $order->getParent()->setTotal($order->getParent()->getTotal());

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

<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\OrderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ComponentInventory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ComponentCollector
     */
    private $collector;

    /**
     * @var array
     */
    private $elements;

    /**
     * ComponentCollector constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->collector = $container->get('component_collector');
        $this->elements = [];
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function update(OrderInterface $order)
    {
        $this->elements = [];

        if ($order->isMaster())
            $this->updateMaster($order);
        else
            $this->updateChildren($order);

        $this->updateElements($order);

        return $this->elements;
    }

    /**
     * @param OrderInterface $order
     */
    private function updateElements(OrderInterface $order)
    {
        foreach ($this->elements as $family => $group) {
            $manager = $this->collector->getManager($family);

            foreach ($group as $code => $quantity) {
                /** @var ComponentInterface $component */
                $component = $manager->findOneBy(['code'=>$code]);

                $qtPrevious = $component->getOrderInventory($order->getPreviousStatus());

                $qtCurrent = $component->getOrderInventory($order->getStatus());

                if ($order->getPreviousStatus() == OrderInterface::STATUS_BUILDING) {

                    if ($order->getStatus() == OrderInterface::STATUS_PENDING
                        || $order->getStatus() == OrderInterface::STATUS_VALIDATED)
                        $component->setOrderInventory($order->getStatus(), $qtCurrent + $quantity);

                } elseif ($order->getPreviousStatus() == OrderInterface::STATUS_PENDING
                    || $order->getPreviousStatus() == OrderInterface::STATUS_VALIDATED){

                    if ($order->getStatus() == OrderInterface::STATUS_PENDING
                        || $order->getStatus() == OrderInterface::STATUS_VALIDATED
                        || $order->getStatus() == OrderInterface::STATUS_REJECTED) {

                        if (!($order->getPreviousStatus() == OrderInterface::STATUS_VALIDATED
                            && $order->getStatus() == OrderInterface::STATUS_REJECTED))
                            $component->setOrderInventory($order->getPreviousStatus(), $qtPrevious - $quantity);

                        if ($order->getStatus() != OrderInterface::STATUS_REJECTED)
                            $component->setOrderInventory($order->getStatus(), $qtCurrent + $quantity);
                    }
                }

                $manager->save($component);
            }
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function updateMaster(OrderInterface $order)
    {
        foreach ($order->getChildrens() as $children)
            $this->updateChildren($children);
    }

    /**
     * @param OrderInterface $children
     */
    private function updateChildren(OrderInterface $children)
    {
        /** @var ElementInterface $element */
        foreach ($children->getElements() as $element) {
            if (!array_key_exists($element->getFamily(),$this->elements))
                $this->elements[$element->getFamily()] = [];
            if (!array_key_exists($element->getCode(),$this->elements[$element->getFamily()]))
                $this->elements[$element->getFamily()][$element->getCode()] = 0;
            $this->elements[$element->getFamily()][$element->getCode()] += $element->getQuantity();
        }
    }
}

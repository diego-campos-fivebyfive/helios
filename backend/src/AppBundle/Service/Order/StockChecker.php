<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StockChecker
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * StockChecker constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function groupComponents(Order $order)
    {
        $group = [];

        if($order->isMaster()) {

            /** @var Order $suborder */
            foreach ($order->getChildrens() as $suborder) {
                $this->addSuborderElementsOnGroup($suborder, $group);
            }
        }

        return $group;
    }

    /**
     * @param array $components
     */
    public function loadStockComponents(array &$groups)
    {
        foreach ($groups as $family => $group) {

            $manager = $this->container->get($family . '_manager');

            foreach ($group as $code => $item) {
                $element = $manager->findOneBy([
                    'code' => $code
                ]);

                if ($element) {
                    $groups[$family][$code]['stock'] = (int) $element->getStock();
                }
            }
        }
    }

    /**
     * @param Order $suborder
     * @param array $group
     */
    private function addSuborderElementsOnGroup(Order $suborder, array &$group = [])
    {
        foreach ($suborder->getElements() as $element) {
            $this->addSuborderElementOnGroup($element, $group);
        }
    }

    /**
     * @param Element $element
     * @param array $group
     */
    private function addSuborderElementOnGroup(Element $element, array &$group = [])
    {
        $family = $element->getFamily();
        $code = $element->getCode();

        if (!array_key_exists($family, $group)) {
            $group[$family] = [];
        }

        if (!array_key_exists($code, $group[$family])) {
            $group[$family][$code] = [
                'description' => $element->getDescription(),
                'quantity' => 0,
                'stock' => 0
            ];
        }

        $group[$family][$code]['quantity'] += $element->getQuantity();
    }
}

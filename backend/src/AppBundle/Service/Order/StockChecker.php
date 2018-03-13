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
     * @var array
     */
    private $families;

    /**
     * StockChecker constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->loadStockControlFamilies();
    }

    /**
     * @param Order $order
     * @return array
     */
    public function checkOutOfStock(Order $order)
    {
        $groups = $this->groupComponents($order);

        $this->loadStockComponents($groups);

        return $this->filterOutOfStock($groups);
    }

    /**
     * Load parameters from platform
     */
    public function loadStockControlFamilies()
    {
        $parameterManager = $this->container->get('parameter_manager');

        $parameters = $parameterManager->findOrCreate('platform_settings');

        $this->families = (array) $parameters->get('stock_control_families');
    }

    /**
     * @param Order $order
     * @return array
     */
    private function groupComponents(Order $order)
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
     * @param array $groups
     */
    private function loadStockComponents(array &$groups)
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
     * @param array $groups
     */
    private function filterOutOfStock(array $groups)
    {
        $componentsOutOfStock = [];

        foreach ($groups as $family => $group) {
            foreach ($group as $code => $item) {
                if ($item['quantity'] > $item['stock']) {
                    $componentsOutOfStock[] = [
                        'code' => $code,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'stock' => $item['stock']
                    ];
                }
            }
        }

        return $componentsOutOfStock;
    }

    /**
     * @param Order $suborder
     * @param array $group
     */
    private function addSuborderElementsOnGroup(Order $suborder, array &$group = [])
    {
        foreach ($suborder->getElements() as $element) {
            if (in_array($element->getFamily(), $this->families)) {
                $this->addSuborderElementOnGroup($element, $group);
            }
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

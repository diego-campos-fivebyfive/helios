<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Service\Order\StockChecker;
use Tests\AppBundle\AppTestCase;

/**
 * @group order_stock_checker
 */
class StockCheckerTest extends AppTestCase
{
    /**
     * @var array
     */
    private $families = [
        'inverter',
        'module',
        'string_box',
        'variety',
        'structure'
    ];

    /**
     * @var StockChecker
     */
    private $stockChecker;

    /**
     * Setup test
     */
    public function setUp()
    {
        $this->stockChecker = $this->service('order_stock_checker');
        $this->clearComponents();
    }

    /**
     * Create test scenario
     */
    public function testDefaultScenario()
    {
        $this->createComponents();

        $order = $this->createOrder();

        $this->testGroupComponents($order);

        $this->testLoadStockComponents($order);
    }

    /**
     * @param Order $order
     */
    private function testLoadStockComponents(Order $order)
    {
        $groups = $this->stockChecker->groupComponents($order);

        $this->stockChecker->loadStockComponents($groups);

        foreach ($groups as $family => $items) {
            foreach ($items as $code => $config) {
                if ($config['stock'] == 50) {
                    $this->assertEquals(50, $config['stock']);
                } else {
                    $this->assertEquals(0, $config['stock']);
                }
            }
        }
    }

    /**
     * @param Order $order
     */
    private function testGroupComponents(Order $order)
    {
        $groups = $this->stockChecker->groupComponents($order);

        foreach ($this->families as $family) {
            $this->assertArrayHasKey($family, $groups);
            foreach ($groups as $family => $items){
                foreach ($items as $code => $config){
                    $this->assertEquals(10, $config['quantity']);
                }
            }
        }
    }

    /**
     * Create master order
     * @return Order
     */
    private function createOrder()
    {
        $manager = $this->manager('order');

        $order = $manager->create();

        $manager->save($order);

        $this->createSuborders($order);

        return $order;
    }

    /**
     * Create suborders for order
     * @param Order $order
     */
    private function createSuborders(Order $order)
    {
        $manager = $this->manager('order');

        for($i = 0; $i < 5; $i++){

            /** @var Order $suborder */
            $suborder = $manager->create();
            $suborder->setParent($order);

            $this->addComponents($suborder);

            $manager->save($suborder);
        }
    }

    /**
     * Add components on suborder
     * @param Order $suborder
     */
    private function addComponents(Order $suborder)
    {
        $components = $this->loadComponents();

        foreach ($components as $family => $items){
            foreach ($items as $component) {

                $element = new Element();
                $element
                    ->setFamily($family)
                    ->setCode($component->getCode())
                    ->setDescription($component->getDescription())
                    ->setQuantity(2)
                ;

                $suborder->addElement($element);
            }
        }
    }

    /**
     * Load persistent components
     * @return array
     */
    private function loadComponents()
    {
        $components = [];
        foreach ($this->families as $family) {
            $components[$family] = $this->manager($family)->findAll();
        }

        return $components;
    }

    /**
     * Create persistent components
     */
    private function createComponents()
    {
        foreach ($this->families as $family){

            $manager = $this->manager($family);
            $max = 5;

            for($i = 0; $i < $max; $i++) {
                $component = $manager->create();

                $code = md5(uniqid(time())) . rand(10, 99);

                $stock = $i == 2 ? null : 50;

                $component
                    ->setCode($code)
                    ->setDescription("This is a component {$code}")
                    ->setStock($stock);

                $manager->save($component, ($i + 1) == $max); // prevent massive persistent operations
            }
        }
    }

    private function clearComponents()
    {
        $groups = $this->loadComponents();

        foreach ($groups as $family => $components) {
            foreach ($components as $component) {
                $this->manager($family)->delete($component);
            }
        }
    }
}

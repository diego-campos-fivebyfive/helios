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

        $this->createParameters();

        $order = $this->createOrder();

        $this->testCheckOutOfStock($order);
    }

    /**
     * @param Order $order
     */
    private function testCheckOutOfStock(Order $order)
    {
        $componentsOutOfStock = $this->stockChecker->checkOutOfStock($order);

        $components = $this->loadComponents();

        // Código que não pode estar no componentsOutOfStock porque familia variety não está
        // nos params
        $code = $components['variety'][0]->getCode();

        foreach ($componentsOutOfStock as $componentOutOfStock) {
            $this->assertLessThan($componentOutOfStock['quantity'], $componentOutOfStock['stock']);
            $this->assertNotEquals($componentOutOfStock['code'], $code);
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

    /**
     * Clear all persistent components
     */
    private function clearComponents()
    {
        $groups = $this->loadComponents();

        foreach ($groups as $family => $components) {
            foreach ($components as $component) {
                $this->manager($family)->delete($component);
            }
        }
    }

    private function createParameters()
    {
        $parameterManager = $this->getContainer()->get('parameter_manager');

        $parameters = $parameterManager->findOrCreate('platform_settings');

        $parameters->set('stock_control_families',[
            "module",
            "inverter",
            "structure",
            "string_box"
        ]);

        $parameterManager->save($parameters);
    }
}

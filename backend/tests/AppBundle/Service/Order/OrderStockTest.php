<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class OrderStockTest
 * @group order_stock
 */
class OrderStockTest extends WebTestCase
{
    /**
     * @var array
     */
    private $families = [
        'module' => [
            'components' => [], // Array of components (dynamic)
            'stocks' => [],     // Array of stock config (dynamic)
            'create' => 1,      // Qauntity for create component
            'stock' => 0,       // Quantiy start stock
            'amount' => 25      // Quantity add order
        ],
        'inverter' => [
            'components' => [],
            'stocks' => [],
            'create' => 2,
            'stock' => 0,
            'amount' => 25
        ],
        'string_box' => [
            'components' => [],
            'stocks' => [],
            'create' => 2,
            'stock' => 0,
            'amount' => 25
        ],
        'structure' => [
            'components' => [],
            'stocks' => [],
            'create' => 10,
            'stock' => 0,
            'amount' => 25
        ],
        'variety' => [
            'components' => [],
            'stocks' => [],
            'create' => 5,
            'stock' => 0,
            'amount' => 50
        ]
    ];

    public function setUp()
    {
        parent::setUp();

        $this->createComponents();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->removeComponents();
    }

    /**
     * Component transactions via Order instance
     */
    public function testTransactElements()
    {
        $manager = $this->manager('order');

        // create orders
        $master = $manager->create();
        $order = $manager->create();

        foreach ($this->families as $family => &$config){

            foreach ($config['components'] as $component) {

                $element = new Element();

                $element
                    ->setCode($component->getCode())
                    ->setFamily($family)
                    ->setMetadata([
                        'id' => $component->getId()
                    ]);

                if (!array_key_exists($component->getId(), $config['stocks']))
                    $config['stocks'][$component->getId()] = 0;

                $element->setQuantity($config['amount']);
                $element->setOrder($order);

                $config['stocks'][$component->getId()] += $config['amount'];
            }
        }

        $master->addChildren($order);

        $manager->save($master);

        $this->service('order_reference')->generate($master);

        $orderStock = $this->service('order_stock');

        // Test default stock
        $this->testStockValuesIsZero();

        // process debit stock
        $orderStock->debit($master);

        // Check stock updated
        foreach ($this->families as $family => &$config){

            $refresher = $this->manager($family)->getObjectManager();

            foreach ($config['components'] as $component) {
                $refresher->refresh($component);
                $this->assertEquals(($config['stocks'][$component->getId()] * -1), $component->getStock());
            }
        }

        // process credit stock
        $orderStock->credit($master);

        // test if stock was debited
        $this->testStockValuesIsZero();
    }

    /**
     * Check all components have stock=0
     */
    private function testStockValuesIsZero()
    {
        // Check stock 0
        foreach ($this->families as $family => $config){

            $refresher = $this->manager($family)->getObjectManager();

            foreach ($config['components'] as $component) {

                $refresher->refresh($component);

                $this->assertEquals(0, $component->getStock());
            }
        }
    }

    /**
     * Create default components
     */
    private function createComponents()
    {
        foreach ($this->families as $family => &$config){

            $manager = $this->manager($family);

            for($i = 0; $i < $config['create']; $i++) {

                $component = $manager->create();

                $component->setCode(md5(uniqid(time())));
                $component->setStock($config['stock']);
                $component->setDescription(sprintf('Test of component %s', $family));

                $manager->save($component);

                $config['components'][] = $component;
            }
        }
    }

    /**
     * Remove created components
     */
    private function removeComponents()
    {
        foreach ($this->families as $family => $config){

            $manager = $this->manager($family);

            foreach ($config['components'] as $component) {
                $manager->delete($component);
            }

            $manager->flush();
        }
    }

    /**
     * @param $family
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($family)
    {
        return $this->service(sprintf('%s_manager', $family));
    }

    /**
     * @param $id
     * @return object
     */
    private function service($id)
    {
        return $this->getContainer()->get($id);
    }
}

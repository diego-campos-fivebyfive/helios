<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class OrderManagerTest
 * @group order_manager
 */
class OrderManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testFluentSetterProperties()
    {
        $data = [
            'reference' => uniqid('REF'),
            'status' => Order::STATUS_BUILDING,
            'description' => 'This is a test order description',
            'note' => 'This is a test order note',
            'power' => 175.25,
            'shippingRules' => ['foo' => 'bar'],
            'sendAt' => new \DateTime('-10 days'),
            'filename' => 'order_file.pdf',
            'contact' => 'Name of contact',
            'email' => 'emailofcontact@gmail.com',
            'phone' => '(11) 99987-5874',
            'customer' => 'Full Customer Name',
            'postcode' => '85472-251',
            'address' => 'The Customer Address, 1578',
            'city' => 'The City',
            'state' => 'KL',
            'cnpj' => '11.111.111./0001-11',
            'ie' => '254.785-58',
            'createdAt' => new \DateTime('-1 week'),
            'updatedAt' => new \DateTime('-10 days')
        ];

        $manager = $this->manager('order');

        $order = $manager->create();

        self::fluentSettersTest($order, $data);
    }

    public function testElementsManagement()
    {
        $manager = $this->getOrderManager();

        $order = $manager->create();

        $this->assertCount(0, $order->getElements()->toArray());

        $order = $manager->create();

        $price = 100;
        $total = 0;
        for($i = 1; $i <= 5; $i++) {

            $element = new Element();
            $element
                ->setCode('ABC')
                ->setQuantity($i)
                ->setUnitPrice($price);

            $order->addElement($element);

            $total += $element->getTotal();
        }

        $order->setMetadata(['memorial' => [
            'version' => 10,
            'isquik_id' => 200
        ]]);

        $metadata = $order->getMetadata('memorial');

        $this->assertArrayHasKey('version',$metadata);

        $this->assertEquals(5, $order->getElements()->count());
        $this->assertEquals($total, $order->getTotal());

        $manager->save($order);
    }

    public function testSelfAssociations()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();
        $order2 = $manager->create();

        $this->assertFalse($order1->isBudget());

        $order1->addChildren($order2);
        $this->assertTrue($order1->isBudget());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSelfAssociationException()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();
        $order2 = $manager->create();
        $order3 = $manager->create();

        $order2->setParent($order1);
        $this->assertTrue($order1->isBudget());

        // TODO: Uncomment to run all tests
        //$order3->addChildren($order1);    via addChildren(parent)
        //$order2->addChildren($order3);    via children::addChildren
        //$order1->setParent($order3);      via parent::setParent()
        $order3->setParent($order3);      //via sameObject
    }

    public function testMetadata()
    {
        $element = new Element();

        // Test value undefined
        $this->assertNull($element->getMetadata('unset_metadata'));

        // Test value exists
        $element->setMetadata(['power' => 25]);
        $this->assertEquals(25, $element->getMetadata('power'));

        // Test with default
        $this->assertEquals(50, $element->getMetadata('with_default', 50));
    }

    /**
     * @return \AppBundle\Manager\OrderManager|object
     */
    private function getOrderManager()
    {
        return $this->getContainer()->get('order_manager');
    }
}

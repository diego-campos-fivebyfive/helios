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

    public function testChildrenAndParentCheck()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();

        $this->assertFalse($order1->isChildren());
        $this->assertFalse($order1->isParent());

        $order2 = $manager->create();
        $order2->setParent($order1);

        $this->assertFalse($order1->isChildren());
        $this->assertTrue($order1->isParent());
        $this->assertTrue($order2->isChildren());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenAddingElementOnMasterOrder()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();
        $order2 = $manager->create();

        $order1->addChildren($order2);

        $element = new Element();

        $order1->addElement($element);
    }

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
            'proforma' => 'order_file.pdf',
            'contact' => 'Name of contact',
            'email' => 'emailofcontact@gmail.com',
            'phone' => '(11) 99987-5874',
            'firstname' => 'Full Customer Name',
            'postcode' => '85472-251',
            'address' => 'The Customer Address, 1578',
            'city' => 'The City',
            'state' => 'KL',
            'cnpj' => '11.111.111./0001-11',
            'ie' => '254.785-58',
            'createdAt' => new \DateTime('-1 week'),
            'updatedAt' => new \DateTime('-10 days'),
            'billingDirect' => true,
            'billingFirstname' => 'Billing Firstname',
            'billingLastname' => 'Billing Lastname',
            'billingContact' => 'Billing Contact',
            'billingCnpj' => '12.457.758/0002-52',
            'billingIe' => '455478-55445.22',
            'billingPhone' => '(42) 9998-7852',
            'billingEmail' => 'email@billing.com',
            'billingPostcode' => '88874-582',
            'billingCity' => 'Billing City',
            'billingState' => 'Billing State',
            'billingDistrict' => 'Billing District',
            'billingStreet' => 'Billing Street',
            'billingNumber' => 'Billing Street',
            'billingComplement' => 'Billing Street'
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
            'version' => 10
        ]]);

        $metadata = $order->getMetadata('memorial');

        $this->assertFalse($order->isMaster());
        $this->assertArrayHasKey('version',$metadata);
        $this->assertEquals(5, $order->getElements()->count());
        $this->assertEquals($total, $order->getSubtotal());

        $manager->save($order);
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
        $this->assertTrue($order1->isMaster());

        // TODO: Uncomment to run all tests
        //$order3->addChildren($order1);    //via addChildren(orderParent)
        //$order2->addChildren($order3);    //via children::addChildren(anotherOrderChildren)
        $order1->setParent($order3);      //via parent::setParent()
        //$order3->setParent($order3);      //via sameObject
    }

    public function testMetadata()
    {
        //$this->markTestSkipped();

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
     * Normalize order files
     * Resolve legacy order files
     */
    public function testFilesManagement()
    {
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->create();

        // Test default
        $this->assertFalse($order->hasFile('payment'));
        $this->assertFalse($order->hasFile('proforma'));

        // Test property normalization
        $this->assertNotEmpty($order->getFiles());
        $this->assertArrayHasKey('payment', $order->getFiles());

        // Add file
        $order->addFile('payment', 'filename.jpg');
        $this->assertNotEmpty($order->getFiles('payment'));
        $this->assertTrue($order->hasFile('payment'));
        $this->assertTrue($order->hasFile('payment', 'filename.jpg'));
        $this->assertFalse($order->hasFile('payment', 'filename2.jpg'));

        // Remove file via index
        $order->removeFile('payment', 0);
        $this->assertEmpty($order->getFiles('payment'));

        $order->addFile('payment', 'filename.png');
        $this->assertNotEmpty($order->getFiles('payment'));
        $this->assertArrayHasKey(0, $order->getFiles('payment')); // Insure first item is key '0'

        // Remove file via name
        $order->removeFile('payment', 'filename.png');
        $this->assertEmpty($order->getFiles('payment'));

        // Add file
        $order->addFile('payment', 'filename1.png');
        $order->addFile('payment', 'filename2.png');
        $order->addFile('payment', 'filename3.png');

        $this->assertTrue($order->hasFile('payment'));
        $this->assertFalse($order->hasFile('proforma'));
        $this->assertEquals('filename3.png', $order->getFile('payment', 2));

        // Add file
        $order->addFile('proforma', 'proforma.pdf');
        $this->assertEquals('proforma.pdf', $order->getFile('proforma'));

        // Test normalizations
        /** @var Order $order2 */
        $order2 = $manager->create();
        $order2->setFilePayment('payment.png');
        $order2->setProforma('proforma.pdf');

        $this->assertNotEmpty($order2->getFiles('payment'));
        $this->assertEquals('proforma.pdf', $order2->getFile('proforma'));
    }

    /**
     * @return \AppBundle\Manager\OrderManager|object
     */
    private function getOrderManager()
    {
        return $this->getContainer()->get('order_manager');
    }
}

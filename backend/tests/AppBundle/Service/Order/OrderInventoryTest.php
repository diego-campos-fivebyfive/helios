<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Service\Order\ComponentCollector;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderClonerTest
 * @group component_inventory
 */
class OrderInventoryTest extends AppTestCase
{

    public $code = 0;

    public function testReturnTypes()
    {
        $module = $this->getFixture('module');
        $this->assertInstanceOf(ComponentInterface::class, $module);

        $this->assertTrue(is_array($module->getOrderInventory()));

        $this->assertEquals(0, $module->getOrderInventory(11));

        $module->setOrderInventory(OrderInterface::STATUS_PENDING, 25);

        $this->assertEquals(25, $module->getOrderInventory(OrderInterface::STATUS_PENDING));
    }

    public function testInventory()
    {
        $componentInventory = $this->getContainer()->get('component_inventory');

        /** @var OrderInterface $master */
        $master = $this->createOrder(2, 2);

        /** @var ElementInterface $element */
        $element = $master->getChildrens()[0]->getElements()[0];
        /** @var ComponentInterface $component */
        $component = $this->manager($element->getFamily())->findOneBy(['code'=>$element->getCode()]);


        $master->setStatus(1);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(4, $component->getOrderInventory(1));

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(8, $component->getOrderInventory(1));

        $master->setStatus(2);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(4, $component->getOrderInventory(1));
        self::assertEquals(4, $component->getOrderInventory(2));


        $master->setStatus(0);
        $master->setStatus(2);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(4, $component->getOrderInventory(1));
        self::assertEquals(8, $component->getOrderInventory(2));


        $master->setStatus(1);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(8, $component->getOrderInventory(1));
        self::assertEquals(4, $component->getOrderInventory(2));

        $master->setStatus(2);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(4, $component->getOrderInventory(1));
        self::assertEquals(8, $component->getOrderInventory(2));

        $master->setStatus(1);
        $master->setStatus(4);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(0, $component->getOrderInventory(1));
        self::assertEquals(8, $component->getOrderInventory(2));

        $master->setStatus(2);
        $master->setStatus(4);

        self::assertNotNull($componentInventory->update($master));

        self::assertEquals(0, $component->getOrderInventory(1));
        self::assertEquals(8, $component->getOrderInventory(2));

    }



    private function createOrder($numberChildrens, $numberElements)
    {
        $manager = $this->manager('order');

        $order = new Order();

        for($i = 0; $i < $numberChildrens; $i++){
            $this->code = 0;
            $children = new Order();
            $repetCode = false;
            for($x = 0; $x < $numberElements; $x++){
                if ($x == $numberElements/2)
                    $repetCode = true;
                $element = $this->createElement($repetCode, ElementInterface::FAMILY_INVERTER);
                $children->addElement($element);
                $element = $this->createElement($repetCode, ElementInterface::FAMILY_MODULE);
                $children->addElement($element);

            }

            $order->addChildren($children);
        }

        $manager->save($order);

        return $order;
    }

    /**
     * @return Element|ElementInterface
     */
    public function createElement($repetCode, $family)
    {
        /** @var ComponentCollector $collector */
        $collector = $this->getContainer()->get('component_collector');
        $manager = $collector->getManager($family);

        $comp = $manager->create();
        $comp->setCode('ABC123-'.$this->code);
        $comp->setStatus(1);

        $manager->save($comp);

        $element = new Element();
        $element->setCode($comp->getCode());
        $element->setDescription('Descrição '.$this->code);
            $this->code++;
        $element->setFamily($family);
        $element->setMetadata(['a'=>"metadata"]);
        $element->setMarkup(0.02);
        $element->setCmv(2);
        $element->setTax(3);
        $element->setDiscount(0.05);

        $element->setQuantity(2);
        $element->setUnitPrice(10);

        return $element;
    }
}

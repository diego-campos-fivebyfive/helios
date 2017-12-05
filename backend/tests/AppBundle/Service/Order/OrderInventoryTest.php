<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderClonerTest
 * @group order_cloner
 */
class OrderClonerTest extends AppTestCase
{

    public $code = 0;

    public function testClonerMaster()
    {
        $orderCloner = $this->getContainer()->get('order_cloner');

        /** @var OrderInterface $master */
        $master = $this->createOrder(3, 10);

        $number = 2;

        $clone = $orderCloner->cloneOrder($master, $number);


        for ($i=0;$i<$number;$i++) {
            /** @var OrderInterface $masterClone */
            $masterClone = $clone[$i];

            self::assertEquals($master->getTotal(), $masterClone->getTotal());

            self::assertEquals(count($master->getChildrens()), count($masterClone->getChildrens()));

            for ($x=0;$x<count($master->getChildrens());$x++) {
                /** @var OrderInterface $childrenMaster */
                $childrenMaster = $master->getChildrens()[$x];
                /** @var OrderInterface $childrenClone */
                $childrenClone = $masterClone->getChildrens()[$x];

                self::assertEquals(count($childrenMaster->getElements()), count($childrenClone->getElements()));
                self::assertEquals($childrenMaster->getTotal(), $childrenClone->getTotal());
                self::assertEquals($childrenMaster->getDescription(), $childrenClone->getDescription());
                self::assertEquals($childrenMaster->getPower(), $childrenClone->getPower());


                for ($y=0;$y<count($childrenMaster->getElements());$y++) {
                    /** @var ElementInterface $elementMaster */
                    $elementMaster = $childrenMaster->getElements()[$y];
                    /** @var ElementInterface $elementClone */
                    $elementClone = $childrenClone->getElements()[$y];

                    self::assertEquals($elementMaster->getTotal(), $elementClone->getTotal());
                    self::assertEquals($elementMaster->getUnitPrice(), $elementClone->getUnitPrice());
                    self::assertEquals($elementMaster->getDiscount(), $elementClone->getDiscount());
                    self::assertEquals($elementMaster->getMetadata(), $elementClone->getMetadata());
                    self::assertEquals($elementMaster->getCode(), $elementClone->getCode());
                    self::assertEquals($elementMaster->getCmv(), $elementClone->getCmv());
                    self::assertEquals($elementMaster->getDescription(), $elementClone->getDescription());
                    self::assertEquals($elementMaster->getFamily(), $elementClone->getFamily());
                    self::assertEquals($elementMaster->getMarkup(), $elementClone->getMarkup());
                    self::assertEquals($elementMaster->getQuantity(), $elementClone->getQuantity());
                    self::assertEquals($elementMaster->getTax(), $elementClone->getTax());
                    self::assertEquals($elementMaster->getTotalCmv(), $elementClone->getTotalCmv());
                    self::assertNotEquals($elementMaster->getOrder(), $elementClone->getOrder());
                }
            }
        }
    }

    public function testClonerSub()
    {
        $orderCloner = $this->getContainer()->get('order_cloner');

        $nChildrens = 3;
        /** @var OrderInterface $master */
        $master = $this->createOrder($nChildrens, 10);

        self::assertEquals(count($master->getChildrens()), $nChildrens);

        $number = 2;

        $clone = $orderCloner->cloneOrder($master->getChildrens()[0], $number);

        self::assertEquals(count($master->getChildrens()), $nChildrens + $number);

        /** @var OrderInterface $childrenMaster */
        $childrenMaster = $master->getChildrens()[0];
        /** @var OrderInterface $childrenClone */
        $childrenClone = $clone[0];

        self::assertEquals($childrenMaster->getParent(), $childrenClone->getParent());

        self::assertEquals(count($childrenMaster->getElements()), count($childrenClone->getElements()));
        self::assertEquals($childrenMaster->getTotal(), $childrenClone->getTotal());
        self::assertEquals($childrenMaster->getDescription(), $childrenClone->getDescription());
        self::assertEquals($childrenMaster->getPower(), $childrenClone->getPower());


        for ($y=0;$y<count($childrenMaster->getElements());$y++) {
            /** @var ElementInterface $elementMaster */
            $elementMaster = $childrenMaster->getElements()[$y];
            /** @var ElementInterface $elementClone */
            $elementClone = $childrenClone->getElements()[$y];

            self::assertEquals($elementMaster->getTotal(), $elementClone->getTotal());
            self::assertEquals($elementMaster->getUnitPrice(), $elementClone->getUnitPrice());
            self::assertEquals($elementMaster->getDiscount(), $elementClone->getDiscount());
            self::assertEquals($elementMaster->getMetadata(), $elementClone->getMetadata());
            self::assertEquals($elementMaster->getCode(), $elementClone->getCode());
            self::assertEquals($elementMaster->getCmv(), $elementClone->getCmv());
            self::assertEquals($elementMaster->getDescription(), $elementClone->getDescription());
            self::assertEquals($elementMaster->getFamily(), $elementClone->getFamily());
            self::assertEquals($elementMaster->getMarkup(), $elementClone->getMarkup());
            self::assertEquals($elementMaster->getQuantity(), $elementClone->getQuantity());
            self::assertEquals($elementMaster->getTax(), $elementClone->getTax());
            self::assertEquals($elementMaster->getTotalCmv(), $elementClone->getTotalCmv());
            self::assertNotEquals($elementMaster->getOrder(), $elementClone->getOrder());
        }
    }

    private function createOrder($numberChildrens, $numberElements)
    {
        $manager = $this->manager('order');

        $order = new Order();

        for($i = 0; $i < $numberChildrens; $i++){
            $children = new Order();

            for($x = 0; $x < $numberElements; $x++){
                $element = $this->createElement();
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
    public function createElement()
    {
        $element = new Element();
        $element->setCode('ABC123-'.$this->code);
        $element->setDescription('Descrição '.$this->code);
        $this->code++;
        $element->setFamily(ElementInterface::FAMILY_INVERTER);
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

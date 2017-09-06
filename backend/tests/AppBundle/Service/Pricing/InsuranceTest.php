<?php

namespace Tests\AppBundle\Service\Pricing;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Service\Pricing\Insurance;
use Tests\AppBundle\AppTestCase;

/**
 * Class InsuranceTest
 * @group insurance
 */
class InsuranceTest extends AppTestCase
{
    /**
     * Test default scenarios
     */
    public function testCalculationFromProject()
    {
        $project = new Project();
        $project->setCostPrice(470800);

        $order = new Order();
        $element = new Element();
        $element
            ->setUnitPrice(42280)
            ->setQuantity(1)
            ->setOrder($order)
        ;

        Insurance::insure($project);
        Insurance::insure($order);

        $this->assertEquals(3060.2, $project->getInsurance());

        $this->assertEquals(42280, $order->getTotal());
        $this->assertEquals(274.82, $order->getInsurance());

        Insurance::remove($order);
        $this->assertEquals(0, $order->getInsurance());
    }
}
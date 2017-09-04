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
    public function testCalculationFromProject()
    {
        $project = new Project();
        $project->setCostPrice(10000);

        $order = new Order();
        $element = new Element();
        $element
            ->setUnitPrice(1000)
            ->setQuantity(20)
            ->setOrder($order)
        ;

        Insurance::insure($project);
        Insurance::insure($order);

        $this->assertEquals(65, $project->getInsurance());

        $this->assertEquals(20000, $order->getTotal());
        $this->assertEquals(130, $order->getInsurance());
    }
}
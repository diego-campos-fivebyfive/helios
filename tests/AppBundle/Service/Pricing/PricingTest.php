<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AppBundle\Service\Pricing;


use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Pricing\Range;
use Tests\AppBundle\AppTestCase;

/**
 * Class PricingTest
 * @group pricing
 */
class PricingTest extends AppTestCase
{
    public function testPricingComponent()
    {
        $range = new Range();
        $range->setCode('dgh')
            ->setInitialPower(0)
            ->setFinalPower(500)
            ->setLevel('platinum')
            ->setPrice(1000)
        ;

        $projectModule = $this->mockProjectModule();

        $projectModule->applyRange($range);

        $this->assertEquals('dgh',$projectModule->getCode());
        $this->assertEquals(10000,$projectModule->getTotalCostPrice());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnSetIncompatibleRange()
    {
        $range = new Range();
        $range->setCode('abc')
            ->setInitialPower(0)
            ->setFinalPower(500)
            ->setLevel('platinum')
            ->setPrice(1000)
        ;

        $projectModule = $this->mockProjectModule();

        $projectModule->applyRange($range);
    }

    private function mockProjectModule()
    {
        $module = new Module();
        $module->setCode('dgh');
        $projectModule = new ProjectModule();
        $projectModule->setModule($module)->setQuantity(10);
        return $projectModule;
    }
}
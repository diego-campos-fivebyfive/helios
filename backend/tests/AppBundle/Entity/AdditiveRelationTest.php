<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\ProjectAdditive;
use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Misc\AdditiveInterface;
use AppBundle\Entity\Order\OrderAdditive;
use AppBundle\Entity\Order\OrderAdditiveInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\AppBundle\AppTestCase;

/**
 * @group additive_relation
 */
class AdditiveRelationTest extends AppTestCase
{
    public function testMockAdditiveCalc()
    {
        $additive = $this->mock(Additive::class, [
            'target' => Additive::TARGET_FIXED,
            'value' => 500
        ]);

        /** @var OrderAdditive $orderAdditive */
        $orderAdditive = $this->mock(OrderAdditive::class, [
            'additive' => $additive
        ]);

        $this->assertEquals(500, $orderAdditive->getTotal());

        $additive2 = $this->mock(Additive::class, [
            'target' => Additive::TARGET_FIXED,
            'value' => 200
        ]);

        $projectAdditive = $this->mock(ProjectAdditive::class, [
            'additive' => $additive2
        ]);

        $this->assertEquals(200, $projectAdditive->getTotal());
    }

    private function mock($class, array $methods = [])
    {
        $mock = new $class();

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($methods as $method=> $return)
            $accessor->setValue($mock, $method, $return);

        return $mock;
    }
}

<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Service\Stock\Identity;
use Tests\AppBundle\AppTestCase;

/**
 * Class SynchronizerTest
 * @group stock
 * @group stock_identity
 */
class IdentityTest extends AppTestCase
{
    /**
     * Identify single object
     */
    public function testSingleIdentity()
    {
        $inverter = $this->getFixture('inverter');
        $this->assertInstanceOf(Inverter::class, $inverter);

        $id = Identity::create($inverter);

        $this->assertEquals(
            sprintf('AppBundle\Entity\Component\Inverter::%s', $inverter->getId()),
            $id
        );
    }

    /**
     * Identify array of objects
     */
    public function testMultipleIdentity()
    {
        $inverter = $this->getFixture('inverter');
        $module = $this->getFixture('module');
        $stringBox = $this->getFixture('string_box');
        $variety = $this->getFixture('variety');

        $data = [$module, $inverter, $stringBox, $variety];

        $ids = Identity::create($data);

        $format = 'AppBundle\Entity\Component\%s::%s';

        $this->assertEquals(sprintf($format, 'Module', $module->getId()), $ids[0]);
        $this->assertEquals(sprintf($format, 'Inverter', $module->getId()), $ids[1]);
        $this->assertEquals(sprintf($format, 'StringBox', $module->getId()), $ids[2]);
        $this->assertEquals(sprintf($format, 'Variety', $module->getId()), $ids[3]);
    }
}

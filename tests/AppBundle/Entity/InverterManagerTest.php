<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\Inverter;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class InverterManagerTest
 * @group inverter
 */
class InverterManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
        $inverter = $this->getFixture('inverter');

        $this->assertNotNull($inverter->getCreatedAt());
    }
}
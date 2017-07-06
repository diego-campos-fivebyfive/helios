<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\Inverter;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class InverterManagerTest
 * @group inverter_manager
 */
class InverterManagerTest extends WebTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
        $data = [
            'code' => self::randomString(8),
            'model' => self::randomString(4),
            'maxDcPower' => self::randomFloat(),
            'maxDcVoltage' => self::randomFloat(),
            'nominalPower' => self::randomFloat(),
            'mpptMaxDcCurrent' => self::randomFloat(),
            'maxEfficiency' => self::randomFloat(),
            'mpptMax' => self::randomFloat(),
            'mpptMin' => self::randomFloat(),
            'mpptNumber' => self::randomFloat(),
            'dataSheet' => self::randomString(15),
            'image' => self::randomString(10),
        ];

        $inverter = new Inverter();
        $this->fluentSettersTest($inverter, $data);

        $this->getContainer()->get('inverter_manager')->save($inverter);

        $this->assertNotNull($inverter->getToken());
        $this->assertNotNull($inverter->getCreatedAt());
    }
}
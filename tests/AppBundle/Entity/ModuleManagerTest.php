<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\Module;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ModuleManagerTest
 * @group module_manager
 */
class ModuleManagerTest extends WebTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
        $data = [
            'model' => self::randomString(4),
            'code' => self::randomString(8),
            'cellNumber' => self::randomInt(),
            'maxPower' => self::randomFloat(),
            'voltageMaxPower' => self::randomFloat(),
            'currentMaxPower' => self::randomFloat(),
            'openCircuitVoltage' => self::randomFloat(),
            'shortCircuitCurrent' => self::randomFloat(),
            'efficiency' => self::randomFloat(),
            'temperatureOperation' => self::randomFloat(),
            'tempCoefficientMaxPower' => self::randomFloat(),
            'tempCoefficientVoc' => self::randomFloat(),
            'tempCoefficientIsc' => self::randomFloat(),
            'length' => self::randomFloat(),
            'width' => self::randomFloat(),
            'cellType' => self::randomString(10),
            'dataSheet' => self::randomString(15),
            'image' => self::randomString(10),
        ];

        $module = new Module();
        $this->fluentSettersTest($module, $data);

        $this->getContainer()->get('module_manager')->save($module);

        $this->assertNotNull($module->getToken());
        $this->assertNotNull($module->getCreatedAt());
    }
}
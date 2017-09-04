<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use AppBundle\Entity\Component\Inverter;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class InverterData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
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
            'mpptConnections' => self::randomInt(5, 15),
            'connectionType' => self::randomString(15),
            'mpptParallel' => false,
            'inProtection' => true,
            'promotional' => false,
            'phases' => 5,
            'dataSheet' => self::randomString(15),
            'image' => self::randomString(10),
            'promotional' => false
        ];

        $inverter = new Inverter();

        $this->fillAndSave($inverter, $data, $manager, 'inverter');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}
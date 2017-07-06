<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\Inverter;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadInverterData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

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
            'dataSheet' => self::randomString(15),
            'image' => self::randomString(10),
        ];

        $inverter = new Inverter();

        self::fluentSetters($inverter, $data);

        $manager->persist($inverter);
        $manager->flush();

        $this->addReference('inverter', $inverter);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}
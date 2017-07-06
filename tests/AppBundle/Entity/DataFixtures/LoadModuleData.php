<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\Module;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadModuleData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
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

        self::fluentSetters($module, $data);

        $manager->persist($module);
        $manager->flush();

        $this->addReference('module', $module);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}
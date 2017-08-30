<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\ProjectGenerator;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Entity\DataFixtures\Component\ModuleData;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group generator_checker
 */
class CheckerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testWithDefaults()
    {
        $this->createModules();

        $defaults = $this->getDefaults();

        $checker = $this->getContainer()->get('generator_checker');

        $checker->checkDefaults($defaults);

        $this->assertFalse($checker->hasError());
    }

    /**
     * @param array $defaults
     * @return array
     */
    private function getDefaults(array $defaults = [])
    {
        return ProjectGenerator::getDefaults($defaults);
    }

    /**
     * @return array
     */
    private function createModules()
    {
        $manager = $this->manager('module');
        $data = ModuleData::getData();
        $modules = [];

        for($i = 0; $i < 10; $i++) {

            if($i > 5){
                $data['promotional'] = true;
            }

            $module = $manager->create();

            self::fluentSetters($module, $data);

            $manager->save($module);

            $modules = [$module];
        }

        return $modules;
    }
}
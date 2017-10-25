<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\Variety;
use AppBundle\Service\ProjectGenerator\Dependency\Loader;
use AppBundle\Service\ProjectGenerator\Dependency\Types;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ResolverTest
 * @group project_generator
 * @group dependency_resolver
 */
class ResolverTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefaultResolverByConfig()
    {
        $project = $this->createProject();
    }

    private function createProject()
    {
        $varieties = $this->createVarieties();
        $inverter = $this->createInverter();
    }

    private function createVarieties($total = 10)
    {
        $manager = $this->manager('variety');
        $varieties = [];
        for ($i = 0; $i < $total; $i++) {
            $variety = $manager->create();
            $variety
                ->setType(Variety::TYPE_CABLE)
                ->setSubtype('foo_bar')
                ->setCode(self::randomString(10))
                ->setDescription(self::randomString(100))
            ;

            $manager->save($variety);

            $varieties[] = $variety;
        }

        return $varieties;
    }

    private function createInverter()
    {
        $manager = $this->manager('inverter');

        $inverter = $manager->create();

        $inverter
            ->setCode(self::randomString(10))
            ->setDescription(self::randomString(100))
        ;

        $manager->save($inverter);

        return $inverter;
    }
}

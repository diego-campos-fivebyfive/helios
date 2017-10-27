<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\Variety;
use AppBundle\Service\ProjectGenerator\Dependency\Loader;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class LoaderTest
 * @group project_generator
 * @group dependency_loader
 */
class LoaderTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefaultLoaderDependencies()
    {
        $fakeIds = 5;
        $varieties = $this->createVarieties();

        $dependencies = [];
        foreach ($varieties as $variety){
            $dependencies['variety'][] = $variety->getId();
        }

        for ($i = 0; $i < $fakeIds; $i++){
            $dependencies['variety'][] = self::randomInt(1000, 1050);
        }

        $loader = Loader::create($this->getContainer());

        $components = $loader->load($dependencies);

        $this->assertArrayHasKey('variety', $components);
        $this->assertCount(count($dependencies['variety']) - $fakeIds, $components['variety']);

        foreach ($components['variety'] as $key => $component){
            $this->assertEquals($component, $varieties[$key]);
        }
    }

    /**
     * @param int $total
     * @return array
     */
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
}

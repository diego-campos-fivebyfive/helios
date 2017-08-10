<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Service\ProjectGenerator\TransformerLoader;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class TransformerLoaderTest
 * @group transformer_loader
 */
class TransformerLoaderTest extends AppTestCase
{
    use ObjectHelperTest;

    public function setUp()
    {
        $this->createTransformers();
    }

    /**
     * Default
     */
    public function testDefaultLoader()
    {
        $power = 301;
        $manager = $this->getManager();
        $loader = new TransformerLoader($manager);

        $transformer = $loader->load($power);

        $this->assertEquals(VarietyInterface::TYPE_TRANSFORMER, $transformer->getType());
        $this->assertEquals(400, $transformer->getPower());
    }

    public function testTransformBehaviorOnProject()
    {
        $power = 200;
        $manager = $this->getManager();
        $loader = new TransformerLoader($manager);

        $project = new Project();

        $this->assertFalse($project->getTransformer());
        $this->assertEmpty($project->getProjectVarieties()->toArray());

        $transformer = $loader->load($power);
        $this->assertInstanceOf(VarietyInterface::class, $transformer);

        $project->setTransformer($transformer);

        $this->assertCount(1, $project->getProjectVarieties()->toArray());

        $transformer2 = $loader->load(100);
        $project->setTransformer($transformer2);

        $this->assertCount(1, $project->getProjectVarieties()->toArray());

        $project->setTransformer(null);

        $this->assertCount(0, $project->getProjectVarieties()->toArray());
    }

    private function createTransformers()
    {
        $manager = $this->getManager();

        for ($i = 1; $i <= 10; $i++){

            $transformer = $manager->create();
            $transformer
                ->setType(VarietyInterface::TYPE_TRANSFORMER)
                ->setCode(self::randomString(10))
                ->setDescription(self::randomString(25))
                ->setPower($i * 100)
            ;

            $manager->save($transformer);
        }
    }

    /**
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function getManager()
    {
        return $this->getContainer()->get('variety_manager');
    }
}
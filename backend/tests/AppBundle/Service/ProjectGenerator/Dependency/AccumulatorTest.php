<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\VarietyInterface;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class CollectionTest
 * @group project_generator
 * @group dependency_collection
 */
class CollectionTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testCollectionBehavior()
    {
        $varieties = $this->createVarieties(10);

        

        //$collection new Collection();
    }

    /**
     * Create a varieties scenario
     *
     * @param $count
     */
    private function createVarieties($count)
    {
        $manager = $this->manager('variety');

        $varieties = [];
        for ($i = 0; $i < $count; $i++){

            /** @var VarietyInterface $variety */
            $variety = $manager->create();

            $variety
                ->setCode(self::randomString(10))
                ->setDescription(self::randomString(100))
                ->setType(VarietyInterface::TYPE_CONNECTOR)
                ->setSubType('bar')
            ;

            $manager->save($variety, $i == ($count - 1));

            $varieties[] = $variety;
        }

        return $varieties;
    }
}

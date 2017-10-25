<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Service\ProjectGenerator\Dependency\Accumulator;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class CollectionTest
 * @group project_generator
 * @group dependency_accumulator
 */
class AccumulatorTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testCollectionBehavior()
    {
        $varieties = $this->createVarieties(10);

        /**
         * [index, quantity]
         */
        $indexToQuantityMapping = [
            [1, 1],
            [2, 3],
            [1, 2],
            [5, 5],
            [1, 5]
        ];

        $accumulator = Accumulator::create();
        $data = [];
        foreach ($indexToQuantityMapping as $mapping){
            $variety = $varieties[$mapping[0]];
            $accumulator->add($variety, $mapping[1]);

            if(!array_key_exists($variety->getId(), $data))
                $data[$variety->getId()] = 0;

            $data[$variety->getId()] += $mapping[1];
        }

        foreach ($accumulator->get(Accumulator::VARIETY) as $item){
            $this->assertEquals($data[$item['component']->getId()], $item['quantity']);
        }

        $items = $accumulator->get(Accumulator::VARIETY);
        $item = $items[$varieties[1]->getId()];

        $this->assertEquals(8, $item['quantity']);
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

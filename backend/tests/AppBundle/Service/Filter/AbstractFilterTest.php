<?php

namespace Tests\AppBundle\Service\Filter;

use AppBundle\Entity\Component\Structure;
use AppBundle\Service\Filter\AbstractFilter;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group filter
 */
class AbstractFilterTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testReadMetadata()
    {
        $this->createComponents();

        $manager = $this->getContainer()->get('doctrine')->getManager();
        //$class = Structure::class; // Uncomment for test via class
        $object = new Structure();  // Test via entity object

        $filter = new AbstractFilter($manager);

        /**
         * Accepted criteria's:
         * equals | notEquals | isNull | notNull | between
         */
        $filter
            ->from($object)
            //->equals('type', 'fixer')
            //->eq('datasheet', null)   // Uncomment for new criteria
            //->between('size', 4, 8)   // Uncomment for new criteria
            //->isNull('datasheet')
            //->notEquals('size', 1)
            ->notNull('datasheet')
        ;

        $result = $filter->get();
        $this->assertCount(5, $result);
    }

    private function createComponents()
    {
        $manager = $this->getContainer()->get('structure_manager');

        for ($i=0; $i < 10; $i++){

            $data = [
                'promotional' => $i % 2 == 0,
                'code' => self::randomString(10),
                'description' => self::randomString(200),
                'size' => $i + 1,
                'type' => $i % 2 != 0 ? 'profile' : 'fixer',
                'subtype' => 'industrial',
                'datasheet' => $i < 5 ? 'datasheet.pdf' : null
            ];

            $component = $manager->create();

            self::fluentSetters($component, $data);

            $manager->save($component, $i == 9);
        }
    }
}
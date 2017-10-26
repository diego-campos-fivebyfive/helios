<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use Tests\AppBundle\AppTestCase;
use AppBundle\Service\ProjectGenerator\Dependency\Indexer;

/**
 * Class IndexerTest
 *
 * @group project_generator
 * @group dependency_indexer
 */
class IndexerTest extends AppTestCase
{
    public function testSimpleIndexingByType()
    {
        $dependencies = [
            ['type' => 'variety', 'id' => 100, 'ratio' => 1],
            ['type' => 'variety', 'id' => 200, 'ratio' => 1],
            ['type' => 'variety', 'id' => 300, 'ratio' => 1],
            ['type' => 'variety', 'id' => 400, 'ratio' => 1],
            ['type' => 'variety', 'id' => 400, 'ratio' => 1],
            ['type' => 'variety', 'id' => 400, 'ratio' => 1],
            ['type' => 'variety', 'id' => 400, 'ratio' => 1],
            ['type' => 'variety', 'id' => 800, 'ratio' => 1],
            ['type' => 'variety', 'id' => 900, 'ratio' => 1],
            ['type' => 'variety', 'id' => 1000, 'ratio' => 1]
        ];

        $indexer = Indexer::process($dependencies);

        $this->assertArrayHasKey('variety', $indexer);
        $this->assertCount(7, $indexer['variety']);
        $this->assertEquals($indexer['variety'][400], 4);
    }

    public function testAdvancedIndexingByType()
    {
        $repeaters = [
            200 => [
                'repeat' => 5,
                'ratio' => 3
            ],
            500 => [
                'repeat' => 7,
                'ratio' => 4
            ]
        ];

        $count = 0;
        $dependencies = [];
        foreach ($repeaters as $id => $config){
            for($i = 0; $i < $config['repeat']; $i++) {

                $dependencies[] = [
                    'type' => 'variety',
                    'id' => $id,
                    'ratio' => $config['ratio']
                ];

                $count++;
            }
        }

        $this->assertCount($count, $dependencies);

        $indexed = Indexer::process($dependencies);

        $varieties = $indexed['variety'];
        foreach ($varieties as $id => $ratio) {
            $this->assertEquals($repeaters[$id]['repeat'] * $repeaters[$id]['ratio'], $ratio);
        }
    }
}

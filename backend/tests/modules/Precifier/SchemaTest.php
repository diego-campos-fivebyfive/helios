<?php

namespace Tests\modules\Precifier;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Precifier\Schema;

/**
 * Class SchemaTest
 * @group dynamo_schema
 */
class SchemaTest extends WebTestCase
{
    //Test
    //Movies
    public function testSchema()
    {
        $params = [
            [
                'attribute' => 'year',
                'keyType' => 'HASH',
                'type' => 'N'
            ],
            [
                'attribute' => 'title',
                'keyType' => 'RANGE',
                'type' => 'S'
            ],

        ];

        // $status = Schema::createTable('Movies2', $params);
        $status = Schema::updateTable('Movies', $params);
        //$status = Schema::deleteTable('Movies2');

        print_r($status);die;
    }
}
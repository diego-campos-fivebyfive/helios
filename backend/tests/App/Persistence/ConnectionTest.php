<?php

namespace Tests\App\Persistence;

use App\Persistence\Connection;
use Tests\App\Sices\SicesTest;

/**
 * @group persistence
 */
class ConnectionTest extends SicesTest
{
    /**
     * @var string
     */
    private $table = 'app_timeline';

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->conn = Connection::create();
    }

    /**
     * Test connection was created with env vars
     */
    public function testConnectionViaEnvVars()
    {
        $connection = Connection::create();

        $this->assertInstanceOf(Connection::class, $connection);
    }

    /**
     * Test connection was created with config
     */
    public function testConnectionViaConfig()
    {
        // 1. Get raw database configuration
        // TODO: Set manualy if necessary
        $config = [
            'host' => getenv('CES_SICES_DATABASE_HOST'),
            'port' => getenv('CES_SICES_DATABASE_PORT'),
            'name' => getenv('CES_SICES_DATABASE_NAME'),
            'user' => getenv('CES_SICES_DATABASE_USER'),
            'pass' => getenv('CES_SICES_DATABASE_PASS')
        ];

        // 2. Create Sices Persistence Connection

        $connection = Connection::create($config);

        $this->assertInstanceOf(Connection::class, $connection);
    }

    /**
     * Test INSERT operation
     */
    public function testInsert()
    {
        $connection = Connection::create();

        $data = $connection->insert('app_timeline', [
            'target' => get_class($this),
            'message' => 'This is a timeline test',
            'attributes' => json_encode(['foo' => 'bar', 'baz' => 'too']),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        $this->assertArrayHasKey('id', $data);
    }

    /**
     * Test SELECT operation
     */
    public function testSelect()
    {
        $connection = Connection::create();

        $randomTarget = sprintf('FooBar%s', md5(uniqid(time())));

        $connection->insert($this->table, [
            'target' => $randomTarget,
            'message' => 'The Message'
        ]);

        $single = $connection->select($this->table, 'target = ?', [$randomTarget]);

        $this->assertCount(1, $single);

        $multiple = $connection->select($this->table, 'target is not null and id > ?', [0]);

        $this->assertGreaterThan(0, count($multiple));
    }

    /**
     * Test UPDATE operation
     */
    public function testUpdate()
    {
        $connection = Connection::create();

        $randomTarget = sprintf('FooBar%s', md5(uniqid(time())));

        $connection->insert($this->table, [
            'target' => $randomTarget,
            'message' => 'The Original Message'
        ]);

        $dataCreated = $connection->select($this->table, 'target = ?', [$randomTarget]);

        $lastItem = current($dataCreated);

        $this->assertNotNull($lastItem['id']);

        $randomTargetModified = 'FooBarModified' . md5(uniqid(time()));

        $connection->update($this->table, [
            'target' => $randomTargetModified,
            'message' => 'The Modified Message'
        ], $lastItem['id']);

        $dataUpdated = $connection->select($this->table, 'target = ?', [$randomTargetModified]);

        $currentItem = current($dataUpdated);

        $this->assertEquals($lastItem['id'], $currentItem['id']);
        $this->assertEquals('The Modified Message', $currentItem['message']);
    }

    /**
     * Test DELETE operation
     */
    public function testDelete()
    {
        $data = $this->createRandom();

        $this->assertTrue($this->conn->delete($this->table, $data['id']));
    }

    /**
     * @return array
     */
    private function createRandom()
    {
        return $this->conn->insert('app_timeline', [
            'target' => get_class($this),
            'message' => 'This is a timeline test',
            'attributes' => json_encode(['foo' => 'bar', 'baz' => 'too']),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }
}

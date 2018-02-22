<?php

use Tests\App\Sices\SicesTest;

/**
 * @group sices_json_cache
 */
class JsonCacheTest extends SicesTest
{
    private $events = [
        0 => [
            'code' => '542',
            'document' => '17774501000128',
            'serial' => '128',
            'invoice' => '000011511',
            'event' => '000',
            'date' => '30012018',
            'time' => '1626'
        ],
        1 => [
            'code' => '542',
            'document' => '17774501000128',
            'serial' => '128',
            'invoice' => '000011511',
            'event' => '001',
            'date' => '30012018',
            'time' => '1443'
        ],
        2 => [
            'code' => '542',
            'document' => '17774501000128',
            'serial' => '128',
            'invoice' => '000011622',
            'event' => '000',
            'date' => '30012018',
            'time' => '1441'
        ],
        3 => [
            'code' => '542',
            'document' => '17774501000128',
            'serial' => '128',
            'invoice' => '000011622',
            'event' => '002',
            'date' => '30012018',
            'time' => '0542'
        ],
        4 => [
            'code' => '542',
            'document' => '17774501000128',
            'serial' => '128',
            'invoice' => '000011633',
            'event' => '001',
            'date' => '30012018',
            'time' => '1442'
        ]
    ];

    public function testJsonCache()
    {
        $jsonCache = $this->createCache();

        self::assertNotNull($jsonCache);

        foreach ($this->events as $event) {
            $jsonCache->incrementInArrayPosition($event['invoice'], $event);
        }

        $data = $jsonCache->all();

        self::assertArrayHasKey('000011622',$data);
    }

    public function testHasKey()
    {
        $jsonCache = $this->createCache();

        self::assertTrue($jsonCache->has('000011622'));
        self::assertTrue($jsonCache->has('000011511'));
    }

    public function testRemoveKey()
    {
//        $jsonCache = $this->createCache();
//
//        self::assertArrayHasKey('000011622',$jsonCache->all());
//
//        self::assertTrue($jsonCache->has('000011622'));
//
//        $jsonCache->remove('000011622');
//
//        self::assertFalse($jsonCache->has('000011622'));
//
//        self::assertArrayNotHasKey('000011622',$jsonCache->all());
    }

    public function testReset()
    {
//        $jsonCache = $this->createCache();
//
//        self::assertNotNull($jsonCache);
//
//        $data = $jsonCache->all();
//
//        self::assertArrayHasKey('000011511',$data);
//
//        self::assertTrue($jsonCache->has('000011511'));
//
//        $jsonCache->reset();
//
//        self::assertArrayNotHasKey('000011511',$jsonCache->all());
//
//        self::assertFalse($jsonCache->has('000011511'));
    }

    private function createCache()
    {
        return \App\Sices\Cache\JsonCache::create('OCOREN');
    }
}

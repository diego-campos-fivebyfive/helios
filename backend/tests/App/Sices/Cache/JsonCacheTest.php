<?php

use Tests\App\Sices\SicesTest;

/**
 * @group sices_json_cache
 */
class JsonCacheTest extends SicesTest
{
    public function testJsonCache()
    {
        $jsonCache = $this->createCache();

        self::assertNotNull($jsonCache);

        $item = [
            'code' => 542,
            'document' => 17302990000115,
            'serial' => 115,
            'invoice' => '000002529',
            'event' => 001,
            'date' => 24012018,
            'time' => '0828'
        ];

        $item2 = [
            'code' => 542,
            'document' => 18547963251852,
            'serial' => 100,
            'invoice' => '000002529',
            'event' => 002,
            'date' => 16022018,
            'time' => '0830'
        ];

        $jsonCache->add('000002529',$item);

        $jsonCache->add('000002529',$item2);

        $jsonCache->add('2',$item);

        $jsonCache->add('2',$item2);

        $data = $jsonCache->all();

        self::assertArrayHasKey('000002529',$data);
    }

    public function testHasKey()
    {
        $jsonCache = $this->createCache();

        self::assertTrue($jsonCache->has('000002529'));
        self::assertTrue($jsonCache->has('2'));
    }

    public function testRemoveKey()
    {
        $jsonCache = $this->createCache();

        self::assertArrayHasKey('000002529',$jsonCache->all());

        self::assertTrue($jsonCache->has('000002529'));

        $jsonCache->remove('000002529');

        self::assertFalse($jsonCache->has('000002529'));

        self::assertArrayNotHasKey('000002529',$jsonCache->all());
    }


    public function testReset()
    {
        $jsonCache = $this->createCache();

        self::assertNotNull($jsonCache);

        $data = $jsonCache->all();

        self::assertArrayHasKey('2',$data);

        self::assertTrue($jsonCache->has('2'));

        $jsonCache->reset();

        self::assertArrayNotHasKey('2',$jsonCache->all());

        self::assertFalse($jsonCache->has('2'));
    }

    private function createCache()
    {
        return \App\Sices\Cache\JsonCache::create('OCOREN');
    }
}

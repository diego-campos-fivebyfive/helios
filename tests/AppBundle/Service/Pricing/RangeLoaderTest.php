<?php

namespace Tests\AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Range;
use Tests\AppBundle\AppTestCase;

/**
 * Class RangeLoaderTest
 * @group range_loader
 */
class RangeLoaderTest extends AppTestCase
{
    public function testLoader()
    {
        $codes = ['ABC', 'DEF', 'GHI', 'JKL', 'MNO', 'PQR', 'STU', 'VXZ'];
        $this->createRanges($codes);

        $power = 100;
        $level = 'platinum';
        $memorial = 1;

        $loader = $this->getContainer()->get('range_loader');

        $ranges = $loader->load($memorial, $power, $level, $codes);

        $this->assertCount(count($codes), $ranges);

        $noRanges = $loader->load($memorial, 1000, $level, $codes);

        $this->assertCount(0, $noRanges);

        // Test with single code - return instance
        $range = $loader->load($memorial, $power, $level, $codes[0]);
        $this->assertInstanceOf(Range::class, $range);

        // Test with single code - return null
        $range = $loader->load($memorial, $power, $level, 'NO_CODE');
        $this->assertNull($range);
    }

    private function createRanges(array $codes)
    {
        $manager = $this->manager('memorial');
        $memorial = $manager->create();

        $start = 0;
        $end = 200;
        $price = 1000;
        $level = 'platinum';

        foreach ($codes as $code){

            $range = new Range();
            $range->setCode($code)
                ->setInitialPower($start)
                ->setFinalPower($end)
                ->setLevel($level)
                ->setPrice($price)
            ;

            $memorial->addRange($range);
        }

        $manager->save($memorial);
    }
}
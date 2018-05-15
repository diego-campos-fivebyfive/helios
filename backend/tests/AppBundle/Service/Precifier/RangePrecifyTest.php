<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\RangeManager;
use AppBundle\Service\Precifier\RangePrecify;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class RangePrecifyTest
 * @group precifier_range_precify
 */
class RangePrecifyTest extends WebTestCase
{
    /**/
    public function testPrecify()
    {
        /** @var RangeManager $manager */
        $manager = $this->getContainer()->get('precifier_range_manager');

        /** @var Range $range */
        $range = $manager->find(7);


        $metadata = $range->getMetadata();
        $costPrice = $range->getCostPrice();
        $level = 'finame';

        $newMetadata = RangePrecify::calculate($metadata, $level, $costPrice);

        self::assertEquals(220.39, $newMetadata[$level][0]['price']);

        $markup = 0.2;
        $powerRange = 900;

        $newMetadata = RangePrecify::calculate($metadata, $level, $costPrice, $markup, $powerRange);

        self::assertEquals(264.46, $newMetadata[$level][$powerRange]['price']);
        self::assertEquals($markup, $newMetadata[$level][$powerRange]['markup']);
    }
}

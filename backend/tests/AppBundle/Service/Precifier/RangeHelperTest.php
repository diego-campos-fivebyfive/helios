<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Range;
use AppBundle\Service\Precifier\RangeHelper;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class RangeLoaderTest
 * @group precifier_range_helper
 */
class RangeHelperTest extends WebTestCase
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testLoad()
    {
        /** @var RangeHelper $rangeHelper */
        $rangeHelper = $this->getContainer()->get('precifier_range_helper');

        /** @var Range $range */
        $range = $rangeHelper->load(7);

        self::assertEquals(true, $range instanceof Range);

        $ranges = $rangeHelper->load([7,8,9]);

        self::assertEquals(3, count($ranges));
        self::assertEquals(true, $ranges[0] instanceof Range);
    }

    /**
     * test ComponentsIds
     */
    public function testComponentsIds()
    {
        /** @var RangeHelper $rangeHelper */
        $rangeHelper = $this->getContainer()->get('precifier_range_helper');

        $groups = $rangeHelper->componentsIds(20);

        self::assertEquals(5, count($groups));

        foreach ($groups as $family) {
            self::assertTrue(is_array($family));
        }
    }
}

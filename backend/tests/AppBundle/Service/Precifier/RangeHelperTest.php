<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Range;
use AppBundle\Service\Precifier\RangeLoader;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class RangeLoaderTest
 * @group precifier_range_loader
 */
class RangeLoaderTest extends WebTestCase
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testLoad()
    {
        /** @var RangeLoader $rangeLoader */
        $rangeLoader = $this->getContainer()->get('precifier_range_loader');

        /** @var Range $range */
        $range = $rangeLoader->load(7);

        self::assertEquals(true, $range instanceof Range);

        $ranges = $rangeLoader->load([7,8,9]);

        self::assertEquals(3, count($ranges));
        self::assertEquals(true, $ranges[0] instanceof Range);
    }
}

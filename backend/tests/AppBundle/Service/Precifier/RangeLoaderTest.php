<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Service\Precifier\Calculator;
use AppBundle\Service\Precifier\MemorialLoader;
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
        /** @var MemorialLoader $memorialLoader */
        $memorialLoader = $this->getContainer()->get('precifier_memorial_loader');

        /** @var Memorial $memorial */
        $memorial = $memorialLoader->load();

        /** @var RangeLoader $rangeLoader */
        $rangeLoader = $this->getContainer()->get('precifier_range_loader');

        /** @var Range $range */
        $range = $rangeLoader->load($memorial, 'inverter', 6418);

        $r = Calculator::identifyRange(15);

        self::assertEquals($range->getMetadata()['partner'][$r]['markup'], 0.1);
        self::assertEquals($range->getMetadata()['partner'][$r]['price'], 380.00);
    }
}

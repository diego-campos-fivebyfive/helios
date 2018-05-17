<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\RangeManager;
use AppBundle\Service\Precifier\RangeNormalizer;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class MemorialNormalizerTest
 * @group new_range_normalizer
 */
class RangeNormalizerTest extends WebTestCase
{
    /**
     * Check and create ranges from product code
     */
    public function testSingleNormalization()
    {
        /** @var RangeManager $manager */
        $manager = $this->getContainer()->get('precifier_range_manager');

        $allBefore = $manager->findAll();

        $c = count($allBefore);

        $memorial = $this->createMemorial();

        $groups = [
            'variety' => [$c+1,$c+2,$c+3],
            'inverter' => [$c+1,$c+2,$c+3]
        ];

        /** @var RangeNormalizer $normalizer */
        $normalizer = $this->getContainer()->get('precifier_range_normalizer');

        $normalizer->generateRanges($memorial, $groups);

        $all = $manager->findAll();

        $this->assertEquals(count($allBefore)+6, count($all));
    }

    /**
     * @return Memorial
     */
    private function createMemorial()
    {
        $manager = $this->getContainer()->get('precifier_memorial_manager');

//        /** @var Memorial $memorial */
//        $memorial = $manager->create();
//
//        $memorial
//            ->setPublishedAt(new \DateTime())
//            ->setStatus(true)
//        ;

        return $manager->find(20);
    }
}

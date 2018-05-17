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
     * create ranges
     */
    public function testGenerateRanges()
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

        //$normalizer->generateRanges($memorial, $groups);

        $all = $manager->findAll();

        //$this->assertEquals(count($allBefore)+6, count($all));
    }

    /**
     * remove ranges
     */
    public function testRemoveRanges()
    {
        /** @var RangeManager $manager */
        $manager = $this->getContainer()->get('precifier_range_manager');

        $allBefore = $manager->findAll();

        $memorial = $this->createMemorial();

        $groups = [
            'inverter' => [3,5,9,10],
            'module' => [1,3,4],
            'string_box' => [6],
            'structure' => [4],
            'variety' => [2,9,10],
        ];

        /** @var RangeNormalizer $normalizer */
        $normalizer = $this->getContainer()->get('precifier_range_normalizer');

        //$normalizer->excludeRanges($memorial, $groups);

        $all = $manager->findAll();

        //$this->assertEquals(count($allBefore)-1, count($all));
    }

    /**
     * remove ranges
     */
    public function testNormalize()
    {
        $memorial = $this->createMemorial();

        /** @var RangeNormalizer $normalizer */
        $normalizer = $this->getContainer()->get('precifier_range_normalizer');

        $normalizer->normalize($memorial);
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

<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
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
        $memorial = $this->createMemorial();

        $groups = [
            'module' => [1 => '123',2 => 'abc',3 => '541'],
            'inverter' => [3=>'158',4=>'875',5=>'fnh'],
        ];

        /** @var RangeNormalizer $normalizer */
        $normalizer = $this->getContainer()->get('precifier_range_normalizer');

        $normalizer->normalize($memorial, $groups);
        //$this->assertEquals(count($powers), $memorial->getRanges()->count());
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

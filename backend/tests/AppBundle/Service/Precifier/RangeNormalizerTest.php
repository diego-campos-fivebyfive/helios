<?php

namespace Tests\AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Service\Pricing\RangeNormalizer;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class MemorialNormalizerTest
 * @group range_normalizer
 */
class RangeNormalizerTest extends AppTestCase
{
    use ObjectHelperTest;

    /**
     * Check and create ranges from product code
     */
    public function testSingleNormalization()
    {
        $normalizer = $this->getNormalizer();
        $memorial = $this->createMemorial();

        $code = 'ABCDE';
        $level = 'single';
        $powers = $normalizer->getPowers();

        $normalizer->normalize($memorial, [$code], [$level]);

        $this->assertEquals(count($powers), $memorial->getRanges()->count());
    }

    /**
     * Check and create ranges from multi product codes
     */
    public function testNormalizationMultiCodes()
    {
        $normalizer = $this->getNormalizer();
        $memorial = $this->createMemorial();

        $codes = ['ABCDE', 'FGHIJ'];
        $levels = ['multi_code'];
        $powers = $normalizer->getPowers();

        $normalizer->normalize($memorial, $codes, $levels);

        $this->assertEquals(count($powers) * count($codes), $memorial->getRanges()->count());
    }

    /**
     * Check and create ranges from multi customer levels
     */
    public function testNormalizationMultiLevels()
    {
        $normalizer = $this->getNormalizer();
        $memorial = $this->createMemorial();

        $codes = ['ABCDE'];
        $levels = ['level_one', 'level_two', 'level_three'];
        $powers = $normalizer->getPowers();

        $normalizer->normalize($memorial, $codes, $levels);

        $this->assertEquals(count($powers) * count($levels), $memorial->getRanges()->count());
    }

    /**
     * Check and create ranges from multi coded and customer levels
     */
    public function testNormalizationMultiCodesAndMultiLevels()
    {
        $normalizer = $this->getNormalizer();
        $memorial = $this->createMemorial();

        $codes = ['ABCDE', 'FGHIJ', 'KHUNG'];
        $levels = ['level_one', 'level_two', 'level_three'];
        $powers = $normalizer->getPowers();

        $normalizer->normalize($memorial, $codes, $levels);

        $this->assertEquals(count($powers) * count($levels) * count($codes), $memorial->getRanges()->count());
    }

    /**
     * Check and create ranges from multi codes and customer levels
     * This is a performance benchmark
     */
    public function testNormalizationMultiCodesAndMultiLevelsBenchmark()
    {
        /**
         * This value represent the multiplier ranges from (count powers) * (count levels) * (count codes)
         *
         * SAMPLE
         * powers: 10
         * max: 30
         * requests: 10 * 30 * 30 = 9000
         * ------------
         * TESTED: 30
         * INSERTIONS: 18.0000
         * TIME: 7.14 SECONDS
         * MEMORY: 156MB
         */
        $max = 30;

        // Creating a random codes
        $codes = [];
        for ($i = 0; $i < $max; $i++){
            $codes[] = $i . '-' . self::randomString(8);
        }

        // Creating a random levels
        $levels = [];
        for ($i = 0; $i < $max; $i++){
            $levels[] = $i . '-' . self::randomString(5);
        }

        $memorial = $this->createMemorial();
        $normalizer = $this->getNormalizer();

        $normalizer->normalize($memorial, $codes, $levels);

        $count = count($normalizer->getPowers()) * $max * $max;

        $this->assertCount($count, $memorial->getRanges()->toArray());
    }

    /**
     * @return object|\AppBundle\Service\Pricing\RangeNormalizer
     */
    private function getNormalizer()
    {
        return $this->getContainer()->get('range_normalizer');
    }

    /**
     * @return Memorial
     */
    private function createMemorial()
    {
        $manager = $this->manager('memorial');

        /** @var Memorial $memorial */
        $memorial = $manager->create();

        $memorial
            ->setStartAt(new \DateTime())
            ->setStatus(true)
            ->setVersion('2222')
        ;

        $manager->save($memorial);

        return $memorial;
    }
}

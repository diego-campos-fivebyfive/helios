<?php

namespace Tests\AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Service\Pricing\MemorialCloner;
use Tests\AppBundle\AppTestCase;

/**
 * Class MemorialClonerTest
 * @group memorial_cloner
 */
class MemorialClonerTest extends AppTestCase
{
    public function testDefaultCloner()
    {
        $memorial = $this->createMemorial();

        $this->assertTrue($memorial->getRanges()->count() > 1);

        $cloner = new MemorialCloner();

        $memorialClone = $cloner->execute($memorial);

        $this->manager('memorial')->save($memorialClone);

        $this->assertNotNull($memorialClone->getId());
        $this->assertInstanceOf(Memorial::class, $memorialClone);
        $this->assertEquals($memorial->getRanges()->count(), $memorialClone->getRanges()->count());

        $rangeProperties = [
            'code',
            'initialPower',
            'finalPower',
            'costPrice',
            'tax',
            'markup',
            'price',
            'level'
        ];

        foreach ($memorialClone->getRanges() as $key => $range){
            foreach ($rangeProperties as $property){
                $getter = 'get' . ucfirst($property);

                $sourceRange = $memorial->getRanges()->offsetGet($key);

                $this->assertEquals($sourceRange->$getter(), $range->$getter());
            }
        }
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
            ->setName('This is a memorial')
        ;

        for ($i = 0; $i < 10; $i++){

            $range = new Range();
            $range
                ->setMarkup(50)
                ->setMemorial($memorial)
                ->setCode('CODE-' . ($i + 200 ))
                ->setCostPrice($i + 100)
                ->setInitialPower($i + 110)
                ->setFinalPower($i + 220)
                ->setLevel('random')
                ->setTax(Range::DEFAULT_TAX)
                ->setPrice($i + 55.75)
            ;
        }

        $manager->save($memorial);

        return $memorial;
    }
}
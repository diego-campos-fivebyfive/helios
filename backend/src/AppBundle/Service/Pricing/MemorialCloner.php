<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Entity\Pricing\RangeInterface;
use AppBundle\Manager\Pricing\MemorialManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MemorialCloner
{
    /**
     * @var MemorialManager
     */
    private $manager;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * @var array
     */
    private $rangeProperties = [
        'initialPower',
        'finalPower',
        'costPrice',
        'markup',
        'level',
        'price',
        'code',
        'tax'
    ];

    /**
     * Adjust memory limit
     * @var string
     */
    private $memory = '512M';

    /**
     * MemorialCloner constructor.
     */
    function __construct(MemorialManager $manager)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->manager = $manager;
    }

    /**
     * @param MemorialInterface $source
     * @return Memorial
     */
    public function execute(MemorialInterface $source)
    {
        ini_set('memory_limit', $this->memory);

        $memorial = new Memorial();

        $memorial
            ->setStatus(Memorial::STATUS_PENDING)
            ->setName(sprintf('%s [clone:%s]', $source->getName(), $source->getId()))
        ;

        /** @var Range $sourceRange */
        foreach ($source->getRanges() as $sourceRange){
            $this->cloneRange($sourceRange, [
                'memorial' => $memorial
            ]);
        }

        $this->manager->save($memorial);

        return $memorial;
    }

    /**
     * @param MemorialInterface $memorial
     * @param $source
     * @param $target
     */
    public function convertLevel(MemorialInterface $memorial, $source, $target)
    {
        $sources = $this->filterRangesByLevel($memorial, $source);

        if(!$sources->isEmpty()) {

            $targets = $this->filterRangesByLevel($memorial, $target);

            $em = $this->manager->getEntityManager();
            foreach ($targets as $range){
                $memorial->removeRange($range);
                $em->remove($range);
            }

            foreach ($sources as $source) {
                $this->cloneRange($source, [
                    'memorial' => $memorial,
                    'level' => $target
                ]);
            }

            $this->manager->save($memorial);
        }
    }

    /**
     * @param MemorialInterface $memorial
     * @param $level
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    private function filterRangesByLevel(MemorialInterface $memorial, $level)
    {
        return $memorial->getRanges()->filter(function (RangeInterface $range) use($level){
            return $level === $range->getLevel();
        });
    }

    /**
     * @param RangeInterface $source
     * @param array $definitions
     * @return Range
     */
    private function cloneRange(RangeInterface $source, array $definitions = [])
    {
        $range = new Range();

        foreach ($this->rangeProperties as $property){

            $value = $this->accessor->getValue($source, $property);

            $this->accessor->setValue($range, $property, $value);
        }

        foreach ($definitions as $property => $value){
            $this->accessor->setValue($range, $property, $value);
        }

        return $range;
    }
}

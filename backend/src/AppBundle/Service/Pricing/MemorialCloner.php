<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Entity\Pricing\Range;
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

            $range = new Range();

            foreach ($this->rangeProperties as $property){

                $value = $this->accessor->getValue($sourceRange, $property);

                $this->accessor->setValue($range, $property, $value);
            }

            $range->setMemorial($memorial);
        }

        $this->manager->save($memorial);

        return $memorial;
    }
}

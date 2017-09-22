<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Entity\Pricing\Range;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MemorialCloner
{
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
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * MemorialCloner constructor.
     */
    function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param MemorialInterface $source
     * @return Memorial
     */
    public function execute(MemorialInterface $source)
    {
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

            $memorial->addRange($range);
        }

        return $memorial;
    }
}

<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\MemorialManager;
use Symfony\Component\PropertyAccess\PropertyAccess;


/**
 * Class RangeLoader
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
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
        'componentId',
        'family',
        'memorial',
        'costPrice',
        'code',
        'metadata'
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
        ini_set('memory_limit', $this->memory);

        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->manager = $manager;
    }

    /**
     * @param Memorial $source
     * @return Memorial
     */
    public function execute(Memorial $source)
    {
        /** @var Memorial $memorial */
        $memorial = $this->manager->create();

        $memorial
            ->setStatus(Memorial::STATUS_PENDING)
            ->setName(sprintf('%s [clone:%s]', $source->getName(), $source->getId()))
            ->setMetadata($source->getMetadata())
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
     * @param Memorial $memorial
     * @param $source
     * @param $target
     */
    public function convertLevel(Memorial $memorial, $source, $target)
    {
        /** @var Range $range */
        foreach ($memorial->getRanges() as $range) {
            $metadata = $range->getMetadata();

            $metadata[$target] = $metadata[$source];

            $range->setMetadata($metadata);
        }

        $this->manager->save($memorial);
    }

    /**
     * @param Range $source
     * @param array $definitions
     * @return Range
     */
    private function cloneRange(Range $source, array $definitions = [])
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

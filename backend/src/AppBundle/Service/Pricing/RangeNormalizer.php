<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Entity\Pricing\Range;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MemorialNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var MemorialInterface
     */
    private $memorial;

    /**
     * @var array
     */
    private $managers = [];

    /**
     * MemorialNormalizer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param MemorialInterface $memorial
     */
    public function setMemorial(MemorialInterface $memorial)
    {
        $this->memorial = $memorial;

        return $this;
    }

    /**
     * @return MemorialInterface
     */
    public function getMemorial()
    {
        return $this->memorial;
    }

    /**
     * @param $code
     * @param $level
     * @param $initialPower
     * @param $finalPower
     * @return Range|mixed
     */
    public function normalize($code, $level, $initialPower, $finalPower)
    {
        $range = $this->memorial->getRanges()->filter(function (Range $range) use($code, $level, $initialPower, $finalPower){
            return $range->hasConfig($code, $level, $initialPower, $finalPower);
        })->last();

        if($range instanceof Range) return $range;

        $manager = $this->manager('range');

        /** @var Range $range */
        $range = $manager->create();

        $range
            ->setCode($code)
            ->setLevel($level)
            ->setInitialPower($initialPower)
            ->setFinalPower($finalPower)
            ->setPrice(0)
        ;

        $manager->save($range);

        return $range;
    }

    /**
     * @param Memorial $memorial
     * @return array
     */
    public function extractCodes(Memorial $memorial)
    {
        return $memorial->getRanges()->map(function (Range $range){
            return $range->getCode();
        })->toArray();
    }

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($id)
    {
        if(!array_key_exists($id, $this->managers)){
            $this->managers[$id] = $this->container->get(sprintf('%s_manager', $id));
        }

        return $this->managers[$id];
    }
}

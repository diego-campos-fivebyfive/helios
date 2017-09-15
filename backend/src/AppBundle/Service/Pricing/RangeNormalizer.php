<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Manager\Pricing\RangeManager;

/**
 * Class RangeNormalizer
 * This class resolves and normalizes ranges from memorial based on codes and levels
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class RangeNormalizer
{
    /**
     * @var MemorialInterface
     */
    private $memorial;

    /**
     * @var RangeManager
     */
    private $manager;

    /**
     * This property control the flush database persistence,
     * allowing grouped transactions
     *
     * @var bool
     */
    private $flush = true;

    /**
     * @var array
     */
    private $powers = [
        [0, 10], [10, 20], [20, 30], [30, 40], [40, 50], [50, 60], [60, 70], [70, 80], [80, 90], [90, 100],
        [100, 200], [200, 300], [300, 400], [400, 500], [500, 600], [600, 700], [700, 800], [800, 900], [900, 1000],
        [1000, 999000]
    ];

    /**
     * RangeNormalizer constructor.
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
        $this->manager = $manager;
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
     * @return array
     */
    public function getPowers()
    {
        return $this->powers;
    }

    /**
     * @param $code
     * @param $level
     */
    public function normalize($code, $level)
    {
        $this->checkMemorial();

        if (is_array($code)) {
            $this->fromCodes($code, $level);
            return;
        }

        if (is_array($level)) {
            $this->fromLevels($code, $level);
            return;
        }

        foreach ($this->powers as $config) {

            list($initialPower, $finalPower) = $config;

            $range = $this->filter($code, $level, $initialPower, $finalPower);

            if (!$range instanceof Range) {
                $this->create($code, $level, $initialPower, $finalPower);
            }
        }

        $this->finish();
    }

    /**
     * @param $code
     * @param $level
     * @param $initialPower
     * @param $finalPower
     * @param $price
     */
    public function create($code, $level, $initialPower, $finalPower, $price = 0)
    {
        $this->checkMemorial();

        $range = $this->manager->create();

        $range
            ->setMemorial($this->memorial)
            ->setCode($code)
            ->setLevel($level)
            ->setInitialPower($initialPower)
            ->setFinalPower($finalPower)
            ->setPrice($price)
        ;

        $this->manager->save($range, false);

        return $range;
    }

    /**
     * @param $code
     * @param $level
     * @param $initialPower
     * @param $finalPower
     * @return null|Range
     */
    public function filter($code, $level, $initialPower, $finalPower)
    {
        return $this->memorial->getRanges()->filter(function (Range $range) use ($code, $level, $initialPower, $finalPower) {
            return $range->hasConfig($code, $level, $initialPower, $finalPower);
        })->last();
    }

    /**
     * @param array $codes
     * @param $level
     */
    private function fromCodes(array $codes, $level)
    {
        $this->flush = false;

        foreach($codes as $code){
            $this->normalize($code, $level);
        }

        $this->flush = true;

        $this->finish();
    }

    /**
     * @param $code
     * @param array $levels
     */
    private function fromLevels($code, array $levels)
    {
        $this->flush = false;

        foreach($levels as $level){
            $this->normalize($code, $level);
        }

        $this->flush = true;

        $this->finish();
    }

    /**
     * Check if memorial is defined
     */
    private function checkMemorial()
    {
        if(!$this->memorial instanceof Memorial)
            throw new \InvalidArgumentException('The Memorial instance is not defined');
    }

    /**
     * Flush entities
     */
    private function finish()
    {
        if($this->flush)
            $this->manager->getEntityManager()->flush();
    }
}

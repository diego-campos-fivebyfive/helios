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
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class RangeNormalizer
 * This class resolves and normalizes ranges from memorial based on codes and levels
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class RangeNormalizer
{
    const FILTER_CACHE = 0;
    const FILTER_DATABASE = 1;
    const FILTER_COLLECTION = 2;

    /**
     * @var RangeManager
     */
    private $manager;

    /**
     * @var int
     */
    private $strategy;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var array
     */
    private $powers = [
        [0, 10], [10, 20], [20, 30], [30, 40], [40, 50], [50, 60], [60, 70], [70, 80], [80, 90], [90, 100],
        [100, 200], [200, 300], [300, 400], [400, 500], [500, 600], [600, 700], [700, 800], [800, 900], [900, 1000],
        [1000, 999000]
    ];

    /**
     * @var array
     */
    private $definitions = [];

    /**
     * @var PropertyAccess
     */
    private $accessor;

    /**
     * RangeNormalizer constructor.
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
        $this->manager = $manager;
        $this->strategy = self::FILTER_CACHE;
    }

    /**
     * @param int $strategy
     * @return RangeNormalizer
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return array
     */
    public function getPowers()
    {
        return $this->powers;
    }

    /**
     * @return array
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param $code
     * @param $level
     */
    public function normalize(Memorial $memorial, array $codes, array $levels, array $definitions = [])
    {
        $this->definitions = $definitions;

        $this->cache($memorial, $codes, $levels);

        foreach ($this->powers as $config) {

            list($initialPower, $finalPower) = $config;

            foreach($levels as $level) {

                foreach ($codes as $code){

                    $range = $this->filter($code, $level, $initialPower, $finalPower);

                    if (!$range instanceof Range) {

                        $range = $this->create($memorial, $code, $level, $initialPower, $finalPower);

                        $cacheKey = $this->createCacheKey($initialPower, $finalPower);

                        $this->cache[$cacheKey][$level][$code] = $range;
                    }

                    $this->checkDefinitions($range);
                }
            }
        }

        $this->manager->flush();
    }

    /**
     * @param Memorial $memorial
     * @param $code
     * @param $level
     * @param $initialPower
     * @param $finalPower
     * @param int $price
     * @return mixed|object|Range
     */
    public function create(Memorial $memorial, $code, $level, $initialPower, $finalPower, $price = 0)
    {
        $range = $this->manager->create();

        $range
            ->setMemorial($memorial)
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
     * @param Memorial|null $memorial
     * @return bool|mixed|null|object|Range
     */
    public function filter($code, $level, $initialPower, $finalPower, Memorial $memorial = null)
    {
        switch ($this->strategy){
            case self::FILTER_CACHE:

                $cacheKey = $this->createCacheKey($initialPower ,$finalPower);

                return array_key_exists($code, $this->cache[$cacheKey][$level]) ? $this->cache[$cacheKey][$level][$code] : false ;

                break;

            case self::FILTER_DATABASE:

                return $this->manager->findOneBy([
                    'memorial' => $memorial,
                    'code' => $code,
                    'level' => $level,
                    'initialPower' => $initialPower,
                    'finalPower' => $finalPower
                ]);

                break;

            case self::FILTER_COLLECTION:

                return $memorial->getRanges()->filter(function (Range $range) use ($code, $level, $initialPower, $finalPower) {
                    return $range->hasConfig($code, $level, $initialPower, $finalPower);
                })->last();

                break;
        }

        return null;
    }

    /**
     * @param $initialPower
     * @param $finalPower
     * @return string
     */
    public function createCacheKey($initialPower, $finalPower)
    {
        return sprintf('%s_%s', $initialPower, $finalPower);
    }

    /**
     * @param Range $range
     */
    private function checkDefinitions(Range $range)
    {
        if(array_key_exists($range->getCode(), $this->definitions)){

            $updatePrice = false;
            $accessor = $this->getAccessor();

            foreach ($this->definitions[$range->getCode()] as $property => $value){

                if($accessor->getValue($range, $property) != $value){
                    $accessor->setValue($range, $property, $value);

                    if('costPrice' == $property){
                        $updatePrice = true;
                    }
                }
            }

            if($updatePrice) {
                $range->updatePrice();
            }
        }
    }

    /**
     * @param Memorial $memorial
     * @param array $codes
     * @param array $levels
     * @return $this
     */
    private function cache(Memorial $memorial, array $codes, array $levels)
    {
        $cache = [];

        $qb = $this->manager->createQueryBuilder();

        foreach ($this->powers as $config) {

            list($initialPower, $finalPower) = $config;

            $cacheKey = $this->createCacheKey($initialPower, $finalPower);

            foreach ($levels as $level){

                $qb->select('r')
                    ->where(
                        $qb->expr()->in('r.code', ':codes')
                    )
                    ->andWhere('r.level = :level')
                    ->andWhere('r.memorial = :memorial')
                    ->andWhere('r.initialPower = :initialPower')
                    ->andWhere('r.finalPower = :finalPower')
                ;

                $ranges = $qb
                    ->setParameters([
                        'codes' => $codes,
                        'level' => $level,
                        'memorial' => $memorial,
                        'initialPower' => $initialPower,
                        'finalPower' => $finalPower
                    ])
                    ->getQuery()
                    ->getResult()
                ;

                $keys = array_map(function(Range $range){
                    return $range->getCode();
                }, $ranges);

                $cache[$cacheKey][$level] = array_combine($keys, $ranges);
            }
        }

        $this->cache = $cache;

        return $this;
    }

    private function getAccessor()
    {
        if(!$this->accessor){
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor;
    }
}

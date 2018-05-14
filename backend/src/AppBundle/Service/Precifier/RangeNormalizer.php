<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\RangeManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class RangeNormalizer
 * This class resolves and normalizes ranges from memorial
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class RangeNormalizer
{
//    const FILTER_CACHE = 0;
//    const FILTER_DATABASE = 1;
//    const FILTER_COLLECTION = 2;

    /**
     * @var RangeManager
     */
    private $manager;

//    /**
//     * @var int
//     */
//    private $strategy;

//    /**
//     * @var array
//     */
//    private $cache = [];

//    /**
//     * @var array
//     */
//    private $powers = [
//        [0, 10], [10, 20], [20, 30], [30, 40], [40, 50], [50, 60], [60, 70], [70, 80], [80, 90], [90, 100],
//        [100, 200], [200, 300], [300, 400], [400, 500], [500, 600], [600, 700], [700, 800], [800, 900], [900, 1000],
//        [1000, 999000]
//    ];

//    /**
//     * @var array
//     */
//    private $definitions = [];

    /**
     * @var PropertyAccess
     */
    private $accessor;

    /**
     * @var string
     */
    private $memory = '512M';

    /**
     * RangeNormalizer constructor.
     * @param RangeManager $manager
     */
    function __construct(RangeManager $manager)
    {
       // ini_set('memory_limit', $this->memory);

        $this->manager = $manager;

       // $this->strategy = self::FILTER_CACHE;
    }

    /**/
    public function normalize(Memorial $memorial, $groups)
    {

        $defautMetadata = [];


        foreach ($groups as $family => $components) {
            $qb = $this->manager->createQueryBuilder();

            $ids = array_keys($components);
            //print_r(array_keys($ids));die;

            $qb->select('r.id')
            ->where('r.family = :family')
            ->andWhere('r.memorial = :memorial')
            ->andWhere($qb->expr()->in('r.componentId', $ids))
            ->setParameters([
                'family' => $family,
                'memorial' => $memorial->getId()
            ]);

            $has = $qb->getQuery()->getResult();

            $without = array_diff($ids, $has);

            foreach ($without as $componentId) {
                $range = new Range();

                $range->setFamily($family);
                $range->setComponentId($componentId);
                $range->setCode($components[$componentId]);
                $range->setCostPrice(0);
                $range->setMemorial($memorial);
                $range->setMetadata($defautMetadata);


                dump($range);
            }
            die;

        }


        //$this->definitions = $definitions;

        //$this->cache($memorial, $codes, $levels);

//        foreach ($this->powers as $config) {
//
//            list($initialPower, $finalPower) = $config;
//
//            foreach($levels as $level) {
//
//                foreach ($codes as $code){
//
//                    $range = $this->filter($code, $level, $initialPower, $finalPower);
//
//                    if (!$range instanceof Range) {
//
//                        $range = $this->create($memorial, $code, $level, $initialPower, $finalPower);
//
//                        $cacheKey = $this->createCacheKey($initialPower, $finalPower);
//
//                        $this->cache[$cacheKey][$level][$code] = $range;
//                    }
//
//                    $this->checkDefinitions($range);
//                }
//            }
//        }

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
}

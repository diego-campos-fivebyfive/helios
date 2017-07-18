<?php

namespace AppBundle\Service\StringBoxCalculator;

use AppBundle\Manager\StringBoxManager;

class StringBoxCalculator
{
    /**
     * @var StringBoxManager
     */
    private $manager;

    /**
     * @inheritDoc
     */
    public function __construct(StringBoxManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $inverters
     * @return array
     */
    public function calculate(array $inverters)
    {
        $collection = [];
        foreach ($inverters as $key => $inverter) {
            $quantity = $inverter['quantity'];
            $strings = $inverter['parallel'];
            $mpptNumber = 1;

            $stringBoxes = $this->findStringBoxes($strings, $mpptNumber);

            self::normalize($stringBoxes, $quantity, $strings);

            foreach($stringBoxes as $stringBox){
                if(!array_key_exists($stringBox['id'], $collection)){
                    $collection[$stringBox['id']] = $stringBox;
                }else{
                    $collection[$stringBox['id']]['quantity'] += $stringBox['quantity'];
                }
            }
        }

        return array_values($collection);
    }

    /**
     * @param array $stringBoxes
     * @param $inverters
     * @param $strings
     */
    public static function normalize(array &$stringBoxes, $inverters, $strings)
    {
        $count = count($stringBoxes);

        for ($j = 1; $j <= 50; $j++) {
            for ($i = 0; $i < count($stringBoxes); $i++) {
                $index = $count - 1 - $i;
                $result = ($stringBoxes[0]['inputs'] * $j) + $stringBoxes[$index]['inputs'];
                if ($result >= $strings) {

                    $stringBoxes[0]['quantity'] = $j * $inverters;
                    $stringBoxes[$index]['quantity'] = 1 * $inverters;

                    if($stringBoxes[$index]['id'] == $stringBoxes[0]['id']){
                        $stringBoxes[0]['quantity'] += $stringBoxes[$index]['quantity'];
                        unset($stringBoxes[$index]);
                    }


                    $stringBoxes = array_values(array_filter($stringBoxes, function($stringBox){
                        return $stringBox['quantity'] > 0;
                    }));

                    break 2;
                }
            }
        }
    }

    /**
     * @param null $inputs
     * @param $outputs
     * @return array
     */
    private function findStringBoxes($inputs = null, $outputs)
    {
        $fields = 's.id, s.inputs, s.outputs';

        $qb = $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($fields)
            ->from($this->manager->getClass(), 's')
        ;

        if($inputs && $outputs) {

            $qb
                ->where('s.inputs >= :inputs')
                ->andWhere('s.outputs >= :outputs')
                ->orderBy('s.inputs', 'asc')
                ->addOrderBy('s.outputs', 'asc')
                ->setParameters([
                    'inputs' => $inputs,
                    'outputs' => $outputs
                ]);

        }else{

            $qb
                //->where('s.inputs >= :inputs')
                ->andWhere('s.outputs >= :outputs')
                ->orderBy('s.inputs', 'desc')
                ->addOrderBy('s.outputs', 'asc')
                ->setParameters([
                    //'inputs' => $inputs,
                    'outputs' => $outputs
                ]);
        }

        $stringBoxes = $qb->getQuery()->getResult(2);

        if(empty($stringBoxes)){
            $stringBoxes = $this->findStringBoxes(null, $outputs);
        }

        array_walk($stringBoxes, function(&$stringBox){
            $stringBox['quantity'] = 0;
        });

        return $stringBoxes;
    }
}
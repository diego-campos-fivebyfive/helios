<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Manager\StringBoxManager;

/**
 * Class StringBoxLoader
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class StringBoxLoader
{
    /**
     * @var StringBoxManager
     */
    private $manager;

    function __construct(StringBoxManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    public function load(&$inputs = null, &$outputs, &$quantity, $maker)
    {
        $fields = 's';

        $qb = $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($fields)
            ->from($this->manager->getClass(), 's')
        ;

        do{

            $quantity += 1;

            $muttableInputs = (int) ceil($inputs / $quantity);
            $muttableOutputs = (int) ceil($outputs / $quantity);

            $parameters = [
                'maker' => $maker,
                'outputs' => $muttableOutputs,
                'inputs' => $muttableInputs
            ];

            $qb
                ->where('s.inputs >= :inputs')
                ->andWhere('s.outputs >= :outputs')
                ->andWhere('s.maker = :maker')
                ->orderBy('s.inputs', 'asc')
                ->addOrderBy('s.outputs', 'asc')
            ;

            $qb->setParameters($parameters);

            $fakeParameters = [];
            CriteriaAggregator::finish($fakeParameters, $qb);

            $stringBoxes = $qb->getQuery()->getResult();

        }while(empty($stringBoxes));

        return $stringBoxes;
    }
}

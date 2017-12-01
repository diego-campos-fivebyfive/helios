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
    public function load(&$inputs = null, &$outputs, $maker)
    {
        $fields = 's';

        $qb = $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($fields)
            ->from($this->manager->getClass(), 's')
        ;

        $parameters = [
            'outputs' => $outputs,
            'maker' => $maker
        ];

        if($inputs && $outputs) {

            $parameters['inputs'] = $inputs;

            $qb
                ->where('s.inputs >= :inputs')
                ->andWhere('s.outputs >= :outputs')
                ->andWhere('s.maker = :maker')
                ->orderBy('s.inputs', 'asc')
                ->addOrderBy('s.outputs', 'asc')
            ;

        }else{

            $qb
                ->andWhere('s.outputs >= :outputs')
                ->andWhere('s.maker = :maker')
                ->orderBy('s.inputs', 'desc')
                ->addOrderBy('s.outputs', 'asc')
            ;
        }

        $fakeParameters = [];
        CriteriaAggregator::finish($fakeParameters, $qb);

        $qb->setParameters($parameters);

        $stringBoxes = $qb->getQuery()->getResult();

        if(empty($stringBoxes)){
            $inputs = null;
            return $this->load($inputs, $outputs, $maker);
        }

        return $stringBoxes;
    }
}

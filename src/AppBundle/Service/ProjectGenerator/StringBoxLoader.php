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
    public function load(&$inputs = null, &$outputs)
    {
        $fields = 's';

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
                ->andWhere('s.maker = :maker')
                ->orderBy('s.inputs', 'asc')
                ->addOrderBy('s.outputs', 'asc')
                ->setParameters([
                    'inputs' => $inputs,
                    'outputs' => $outputs
                ]);

        }else{

            $qb
                ->andWhere('s.outputs >= :outputs')
                ->andWhere('s.maker = :maker')
                ->orderBy('s.inputs', 'desc')
                ->addOrderBy('s.outputs', 'asc')
                ->setParameters([
                    'outputs' => $outputs
                ]);
        }

        $qb->setParameter('maker', 61124);

        $stringBoxes = $qb->getQuery()->getResult();

        if(empty($stringBoxes)){
            $inputs = null;
            return $this->load($inputs, $outputs);
        }

        return $stringBoxes;
    }
}
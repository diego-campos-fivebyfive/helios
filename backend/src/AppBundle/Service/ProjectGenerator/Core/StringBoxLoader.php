<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\StringBox;
use AppBundle\Manager\StringBoxManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class StringBoxLoader
*  @author Gianluca Bine <gian_bine@hotmail.com>
*/
class StringBoxLoader extends AbstractLoader
{

    /**
     * @var string
     */
    protected $properties = 's.id, s.inputs, s.outputs, s.generatorLevels levels';

    /**
     * @return array
     */
    public function all()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select($this->properties)
            ->addOrderBy('s.inputs', 'ASC')
            ->addOrderBy('s.outputs', 'ASC');

        if (!is_null($this->config['maker'])) {
            $qb->andWhere($qb->expr()->eq('s.maker', $this->config['maker']));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function alternatives()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb2 = $this->manager->createQueryBuilder();

        $alternatives = array_map(function ($alt) {
            return current($alt);
        }, $qb2->select("DISTINCT(s.alternative)")
            ->where('s.alternative > 0')
            ->getQuery()->getResult()
        );

        if ($alternatives) {
            $results = $qb->select($this->properties)
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->in(
                            's.id',
                            $alternatives
                        ),
                        's.maker != :maker'
                    ))
                ->addOrderBy('s.inputs', 'ASC')
                ->addOrderBy('s.outputs', 'ASC')
                ->setParameter('maker', $this->config['maker'])
                ->getQuery()->getResult();

            return $this->formatKeys($results);
        }

        return [];
    }

}

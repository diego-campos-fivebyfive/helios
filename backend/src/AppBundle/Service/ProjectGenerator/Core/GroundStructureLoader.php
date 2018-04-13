<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use App\Generator\Structure\Ground;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Manager\StringBoxManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class StringBoxLoader
 *  @author Gianluca Bine <gian_bine@hotmail.com>
 */
class GroundStructureLoader extends AbstractLoader
{

    /**
     * @param array $groundData
     * @return array
     */
    public function load(array $groundData)
    {
        $groundTypesWithoutSize = [
            'ground_portico',
            'ground_clamps',
            'ground_screw',
            'ground_diagonal_union'
        ];

        $groundStructures = [];

        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select()
            ->andWhere($qb->expr()->in('s.type', $groundTypesWithoutSize));


        $results = $qb->getQuery()->getResult();

        foreach ($results as $result) {
            $groundStructures[] = $result;
        }

        $keys = array_keys($groundData);

        foreach ($keys as $key) {
            /** @var QueryBuilder $qb2 */
            $qb2 = $this->config['manager']->createQueryBuilder();

            if ($key == 'diagonalGapSize') {
                $qb2->select()
                    ->andWhere($qb->expr()->eq('s.type', $qb->expr()->literal('ground_diagonal')))
                    ->andWhere($qb->expr()->eq('s.size', $groundData['diagonalGapSize']));

                $results = $qb2->getQuery()->getResult();

                foreach ($results as $result) {
                    $groundStructures[] = $result;
                }
            }
            if ($key == 'mainCrossSize') {
                $qb2->select()
                    ->andWhere($qb->expr()->eq('s.type', $qb->expr()->literal('ground_cross')))
                    ->andWhere($qb->expr()->eq('s.size', $groundData['mainCrossSize']));

                $results = $qb2->getQuery()->getResult();

                foreach ($results as $result) {
                    $groundStructures[] = $result;
                }
            }
            if ( $key == 'balanceCrossSize') {
                $qb2->select()
                    ->andWhere($qb->expr()->eq('s.type', $qb->expr()->literal('ground_cross')))
                    ->andWhere($qb->expr()->eq('s.size', $groundData['balanceCrossSize']));

                $results = $qb2->getQuery()->getResult();

                foreach ($results as $result) {
                    $groundStructures[] = $result;
                }
            }
        }

        return $groundStructures;
    }

    /**
     * @return array
     */
    public function all()
    {
        return  [];
    }

    /**
     * @return array
     */
    public function alternatives()
    {
        return [];
    }

}

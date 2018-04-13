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
            if ($key == 'mainCrossSize' || $key == 'balanceCrossSize' || $key == 'diagonalGapSize') {

                $type = $key == 'diagonalGapSize' ? 'ground_diagonal' : 'ground_cross';

                $this->loadStructuresWithSize($type, $groundData[$key], $groundStructures);
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

    /**
     * @param $type
     * @param $size
     * @param $groundStructures
     */
    private function loadStructuresWithSize($type, $size, &$groundStructures)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select()
            ->andWhere($qb->expr()->eq('s.type', $qb->expr()->literal($type)))
            ->andWhere($qb->expr()->eq('s.size', $size));

        $results = $qb->getQuery()->getResult();

        foreach ($results as $result) {
            $groundStructures[] = $result;
        }
    }

}

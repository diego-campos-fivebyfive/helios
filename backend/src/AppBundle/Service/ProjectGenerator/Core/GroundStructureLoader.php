<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use App\Generator\Structure\Ground;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
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

        $typeKey = [
            'ground_portico' => 'porticoQuantity',
            'ground_clamps' => 'clampsQuantity',
            'ground_screw' => 'screwQuantity',
            'ground_diagonal_union' => 'diagonalUnionQuantity'
        ];

        /** @var Structure $structure */
        foreach ($results as $structure) {
            $groundStructures[] = [
                'structure' => $structure,
                'quantity' => $groundData[$typeKey[$structure->getType()]]
            ];
        }

        $groundDataKeys = array_keys($groundData);

        $relationship = [
            'mainCrossSize' => 'mainCrossQuantity',
            'balanceCrossSize' => 'balanceCrossQuantity',
            'diagonalGapSize' => 'diagonalQuantity'
        ];

        foreach ($groundDataKeys as $groundDataKey) {
            if ($groundDataKey == 'mainCrossSize' || $groundDataKey == 'balanceCrossSize' || $groundDataKey == 'diagonalGapSize') {

                $quantity = $groundData[$relationship[$groundDataKey]];

                $type = $groundDataKey == 'diagonalGapSize' ? 'ground_diagonal' : 'ground_cross';

                $this->loadGroundStructuresWithSize($type, $groundData[$groundDataKey], $quantity, $groundStructures);
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
     * @param $quantity
     * @param $groundStructures
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function loadGroundStructuresWithSize($type, $size, $quantity, &$groundStructures)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select()
            ->andWhere($qb->expr()->eq('s.type', $qb->expr()->literal($type)))
            ->andWhere($qb->expr()->eq('s.size', $size));

        $qb->setMaxResults(1);

        $structure = $qb->getQuery()->getOneOrNullResult();

        $groundStructures[] = [
            'structure' => $structure,
            'quantity' => $quantity
        ];
    }

}

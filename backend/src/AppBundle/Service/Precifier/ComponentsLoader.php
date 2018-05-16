<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Component\ComponentInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class ComponentsLoader
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class ComponentsLoader
{
    /** @var ContainerInterface $container */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array|null $loadFamilies
     * @return array
     */
    public function loadAll(array $loadFamilies = null)
    {
        $families = $loadFamilies ? $loadFamilies : $this->getFamilies();
        $components = [];

        foreach ($families as $family) {
            $manager = $this->container->get("{$family}_manager");

            /** @var QueryBuilder $qb */
            $qb = $manager->createQueryBuilder();
            $alias = $qb->getRootAlias();

            $qb->select("{$alias}.id");

            $ids = array_map('current', $qb->getQuery()->getResult());

            $components[$family] = $ids;
        }

        return $components;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function loadByIds(array $groups)
    {
        $components = [];

        foreach ($groups as $family => $componentIds) {
            $manager = $this->container->get("{$family}_manager");

            $field = $family === 'module' || $family === 'inverter' ? 'model' : 'description';

            /** @var QueryBuilder $qb */
            $qb = $manager->createQueryBuilder();
            $alias = $qb->getRootAlias();

            $qb->select("{$alias}.id, {$alias}.{$field} as description");
            $qb->andWhere(
                $qb->expr()->in("{$alias}.id", $componentIds)
            );

            $results = $qb->getQuery()->getResult();

            foreach ($results as $result) {
                $components[$family][$result['id']]['description'] = $result['description'];
            }
        }

        return $components;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function loadByNotInIds(array $groups)
    {
        $components = [];

        foreach ($groups as $family => $componentIds) {
            $manager = $this->container->get("{$family}_manager");

            /** @var QueryBuilder $qb */
            $qb = $manager->createQueryBuilder();
            $alias = $qb->getRootAlias();

            $qb->select("{$alias}.id");
            $qb->andWhere(
                $qb->expr()->notIn("{$alias}.id", $componentIds)
            );

            $results = $qb->getQuery()->getResult();

            $components[$family] = array_map('current', $results);
        }

        return $components;
    }

    /**
     * @return array
     */
    private function getFamilies()
    {
        return [
            ComponentInterface::FAMILY_MODULE,
            ComponentInterface::FAMILY_INVERTER,
            ComponentInterface::FAMILY_STRING_BOX,
            ComponentInterface::FAMILY_STRUCTURE,
            ComponentInterface::FAMILY_VARIETY
        ];
    }
}

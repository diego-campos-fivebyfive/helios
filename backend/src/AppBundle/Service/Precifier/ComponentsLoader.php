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
     * @return array
     */
    public function load()
    {
        $families = $this->getFamilies();
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

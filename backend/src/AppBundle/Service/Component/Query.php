<?php

namespace AppBundle\Service\Component;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Query
{
    /**
     * @var array
     */
    private $managers = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Query constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $criteria
     * @return mixed
     */
    public function fromCriteria(array $criteria = [])
    {
        $criteria = array_merge([
            'page' => 1,
            'family' => null,
            'like' => null
        ], $criteria);

        $families = ['inverter', 'module', 'string_box', 'structure', 'variety'];

        $family = $criteria['family'];
        $like = $criteria['like'];
        $page = (int) $criteria['page'];
        $perPage = 10;

        if($family) $families = [$family];

        $data = [];
        foreach ($families as $family){

            $manager = $this->manager($family);
            $qb = $manager->createQueryBuilder();
            $alias = $manager->alias();
            $field = in_array($family, ['inverter', 'module']) ? 'model' : 'description';

            if($like) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            sprintf('%s.%s', $alias, $field),
                            $qb->expr()->literal('%' . $like . '%')
                        ),
                        $qb->expr()->like(
                            sprintf('%s.code', $alias),
                            $qb->expr()->literal('%' . $like . '%')
                        )
                    )
                );
            }

            $result = $qb->getQuery()->getResult();

            $data = array_merge($data, $result);
        }

        $paginator = $this->getPaginator();

        $pagination = $paginator->paginate(
            $data,
            $page,
            $perPage
        );

        return $pagination;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function fromRequest(Request $request)
    {
        return $this->fromCriteria($request->query->all());
    }

    /**
     * @param $family
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($family)
    {
        if(!array_key_exists($family, $this->managers))
            $this->managers[$family] = $this->container->get(sprintf('%s_manager', $family));

        return $this->managers[$family];
    }

    /**
     * @return object
     */
    private function getPaginator()
    {
        return $this->container->get('knp_paginator');
    }
}

<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApiBundle\Common;

use AppBundle\Manager\AbstractManager;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class handles the request and formats the output of paged data
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Handler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param array $converts
     * @return array
     */
    public function handleRequest(Request $request, array $converts = [])
    {
        list($method, $target) = explode('_', $request->attributes->get('_route'));

        $manager = $this->manager($target);

        if(!$manager instanceof AbstractManager){
            throw new \InvalidArgumentException(sprintf('The manager %s not found', $target));
        }

        $qb = $manager->createQueryBuilder();

        $pagination = $this->paginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->pager()->paginate($pagination, $converts);
    }

    /**
     * @return object|\Knp\Component\Pager\PaginatorInterface
     */
    protected function paginator()
    {
        return $this->container->get('knp_paginator');
    }

    /**
     * @param $target
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($target)
    {
        $serviceId = Inflector::singularize($target);

        return $this->container->get(sprintf('%s_manager', $serviceId), ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    /**
     * @return object|Pager
     */
    private function pager()
    {
        return $this->container->get('api_pager');
    }
}
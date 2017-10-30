<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Stock;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Provider
 * This class load and caching services for stock management
 * Prevent multi service request
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Provider
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $services = [];

    /**
     * Provider constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $service
     * @return object
     */
    public function get($service)
    {
        if(!$this->has($service)){
            $this->services[$service] = $this->container->get($service);
        }

        return $this->services[$service];
    }

    /**
     * @param $name
     * @return object
     */
    public function manager($name)
    {
        return $this->get(sprintf('%s_manager', $name));
    }

    /**
     * @param $service
     * @return bool
     */
    public function has($service)
    {
        return array_key_exists($service, $this->services);
    }
}

<?php

namespace AppBundle\Service\Cart;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CartPoolCreate
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * cartPoolTransform constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($code, $account, $kits)
    {

    }
}

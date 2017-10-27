<?php

namespace AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Manager\AbstractManager;
use AppBundle\Entity\Component\ComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Loader
 * @package AppBundle\Service\ProjectGenerator\Dependency
 */
class Loader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $managers = [];

    /**
     * @var array
     */
    private $components = [];

    /**
     * Loader constructor.
     * @param ContainerInterface $container
     */
    private function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $id
     * @param $type
     * @return array
     */
    public function load(array $dependencies)
    {
        foreach ($dependencies as $type => $dependency){
            $this->fetch($type, $dependency);
        }

        return $this->components;
    }

    /**
     * @param $type
     * @param array $dependencies
     */
    private function fetch($type, array $dependencies = [])
    {
        if(!array_key_exists($type, $this->components))
            $this->components[$type] = [];

        foreach ($dependencies as $dependency){
            $this->resolve($type, $dependency);
        }
    }

    /**
     * @param $type
     * @param $id
     */
    private function resolve($type, $id)
    {
        if(!array_key_exists($id, $this->components[$type])){

            $component = $this->find($type, $id);

            if($component instanceof ComponentInterface)
                $this->add($type, $component);
        }
    }

    /**
     * @param $type
     * @param $id
     * @return null|object
     */
    private function find($type, $id)
    {
        return $this->manager($type)->find($id);
    }

    /**
     * @param $type
     * @param ComponentInterface $component
     */
    private function add($type, ComponentInterface $component)
    {
        $this->components[$type][] = $component;
    }

    /**
     * @param $type
     * @return AbstractManager|object
     */
    private function manager($type)
    {
        if(!array_key_exists($type, $this->managers))
            $this->managers[$type] = $this->container->get(sprintf('%s_manager', $type));

        return $this->managers[$type];
    }

    /**
     * @param ContainerInterface $container
     * @return Loader
     */
    public static function create(ContainerInterface $container)
    {
        return new self($container);
    }
}

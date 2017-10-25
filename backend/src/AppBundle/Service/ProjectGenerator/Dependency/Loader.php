<?php

namespace AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Manager\AbstractManager;
use AppBundle\Entity\Component\ComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $id
     * @param $type
     * @return null|ComponentInterface
     */
    public function load($id, $type)
    {
        $manager = $this->manager($type);

        if(!array_key_exists($type, $this->components))
            $this->components[$type] = [];

        if(!array_key_exists($id, $this->components[$type])){

            $component = $manager->find($id);

            if($component instanceof ComponentInterface) {
                $this->components[$type][$id] = $component;
            }
        }

        return array_key_exists($id, $this->components[$type])  ? $this->components[$type][$id] : null ;
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
}

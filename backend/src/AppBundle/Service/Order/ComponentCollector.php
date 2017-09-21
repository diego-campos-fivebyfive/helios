<?php

namespace AppBundle\Service\Order;


use Symfony\Component\DependencyInjection\ContainerInterface;

class ComponentCollector
{
    /**
     * @var array
     */
    private $components = [];

    /**
     * @var array
     */
    private $managers = [
        'inverter' => null,
        'module' => null,
        'string_box' => null,
        'variety' => null,
        'structure' => null
    ];
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ComponentCollector constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $code
     * @return mixed|null|object
     */
    public function fromCode($code)
    {
        if (array_key_exists($code, $this->components)) {
            return $this->components[$code];
        }

        foreach ($this->managers as $type => $manager) {

            $manager = $this->resolveManager($type);

            if(null != $component = $manager->findOneBy(['code' => $code])){
                $this->components[$code] = $component;
                return $component;
                break;
            }
        }

        return null;
    }

    /**
     * @param $type
     * @return \AppBundle\Manager\AbstractManager|null
     */
    public function getManager($type)
    {
        return $this->resolveManager($type);
    }

    /**
     * @return array
     */
    public function getManagers()
    {
        foreach ($this->managers as $type => $manager){
            $this->resolveManager($type);
        }

        return $this->managers;
    }

    /**
     * @param $type
     * @return \AppBundle\Manager\AbstractManager
     */
    private function resolveManager($type)
    {
        if(null == $manager = $this->managers[$type]){
            $this->managers[$type] = $this->container->get(sprintf('%s_manager', $type));
        }

        return $this->managers[$type];
    }
}

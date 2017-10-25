<?php

namespace AppBundle\Service\ProjectGenerator\Dependency;

/**
 * Class Resolver
 * @package AppBundle\Service\ProjectGenerator\Dependency
 */
class Resolver
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var Accumulator
     */
    private $accumulator;

    /**
     * Resolver constructor.
     * @param Loader $loader
     */
    function __construct(Loader $loader)
    {
        $this->loader = $loader;
        $this->accumulator = Accumulator::create();
    }

    /**
     * @param array $mapping
     * @return array
     */
    public function fromMapping(array $mapping)
    {
        $types = [];
        foreach ($mapping as $config){
            if(null != $component = $this->loader->load($config['id'], $config['type'])){
                $this->accumulator->add($component, $config['quantity']);
                $types[$config['type']] = $this->accumulator->get($config['type']);
            }
        }

        return $types;
    }

    /**
     * @return Resolver
     */
    public static function create(Loader $loader)
    {
        return new self($loader);
    }
}

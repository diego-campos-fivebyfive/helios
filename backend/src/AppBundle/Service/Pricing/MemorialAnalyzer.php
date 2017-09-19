<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MemorialAnalyzer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $collection = [
        'components' => [
            'module' => [
                'enabled' => false,
                'label' => 'Modules',
                'items' => []
            ],
            'inverter' => [
                'enabled' => false,
                'label' => 'Inverters',
                'items' => []
            ],
            'string_box' => [
                'enabled' => false,
                'label' => 'String Box',
                'items' => []
            ],
            'structure' => [
                'enabled' => false,
                'label' => 'Structures',
                'items' => []
            ],
            'variety' => [
                'enabled' => false,
                'label' => 'Varieties',
                'items' => []
            ]
        ]
    ];

    /**
     * @var array
     */
    private $managers = [
        'module' => null,
        'inverter' => null,
        'string_box' => null,
        'structure' => null,
        'variety' => null,
    ];

    /**
     * @var array
     */
    private $criteria = [
        'status' => true,
        'available' => true
    ];

    /**
     * @var RangeNormalizer
     */
    private $normalizer;

    /**
     * @var string
     */
    private $level;

    /**
     * MemorialAnalyzer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->initNormalizer();
    }

    /**
     * @param array $config
     */
    public function analyze(array $config)
    {
        /** @var Memorial $memorial */
        $memorial = $config['memorial'];
        $this->level = $config['level'];
        $types = $config['components'];
        $codes = [];

        foreach ($types as $type){

            $this->collection['components'][$type]['enabled'] = true;

            $components = $this->manager($type)->findBy($this->criteria);

            $this->updateItems($type, $components);

            $codes = array_merge($codes, $this->filterCodes($components));
        }

        $this->normalizer->normalize($memorial, $codes, [$this->level]);

        $this->finishCollection();

        return $this->collection;
    }

    /**
     * Finish analyzed collection
     */
    private function finishCollection()
    {
        $cache = $this->normalizer->getCache();
        $powers = $this->normalizer->getPowers();

        foreach ($this->collection['components'] as $componentType => $componentConfig) {

            if(!$componentConfig['enabled']){
                unset($this->collection['components'][$componentType]);
                continue;
            }

            $items = $componentConfig['items'];

            foreach ($items as $itemConfig) {

                /** Module | Inverter | StringBox | Structure | Variety */
                /** @var \AppBundle\Entity\Component\InverterInterface $product */
                $product = $itemConfig['product'];

                foreach ($powers as $power) {

                    list($initialPower, $finalPower) = $power;

                    $cacheKey = $this->normalizer->createCacheKey($initialPower, $finalPower);

                    $range = $cache[$cacheKey][$this->level][$product->getCode()];

                    $this->collection['components'][$componentType]['items'][$product->getId()]['ranges'][] = $range;
                }
            }
        }
    }

    /**
     * @param $type
     * @param array $components
     */
    private function updateItems($type, array $components)
    {
        foreach ($components as $component){
            $this->collection['components'][$type]['items'][$component->getId()] = [
                'product' => $component
            ];
        }
    }

    /**
     * @param array $components
     * @return array
     */
    private function filterCodes(array $components)
    {
        $codes = [];
        foreach ($components as $component) {
            $codes[] = $component->getCode();
        }

        return $codes;
    }

    /**
     * Initialize normalize references
     */
    private function initNormalizer()
    {
        $this->normalizer = $this->container->get('range_normalizer');
        $this->collection['powers'] = $this->normalizer->getPowers();
    }

    /**
     * @param $id
     * @return mixed|object|\AppBundle\Manager\AbstractManager
     */
    private function manager($id)
    {
        if(!$this->managers[$id]){
            $this->managers[$id] = $this->container->get(sprintf('%s_manager', $id));
        }

        return $this->managers[$id];
    }
}

<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
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
     * @var string
     */
    private $memory = '512M';

    /**
     * MemorialAnalyzer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        ini_set('memory_limit', $this->memory);

        $this->container = $container;

        $this->initNormalizer();
    }

    /**
     * @param array $config
     * @return array
     * @throws \Throwable
     * @throws \TypeError
     */
    public function analyze(array $config)
    {
        /** @var Memorial $memorial */
        $memorial = $config['memorial'];
        $this->level = $config['level'];
        $types = $config['components'];
        $data['tags'] = [];
        $data['codes'] = [];

        foreach ($types as $type) {

            $this->collection['components'][$type]['enabled'] = true;

            $manager = $this->manager($type);
            $qb = $manager->createQueryBuilder();

            $qb->andWhere(
                $qb->expr()->like(sprintf('%s.princingLevels', $manager->alias()),
                    $qb->expr()->literal('%"' . $config['level'] . '"%'))
            );

            $components = $qb->getQuery()->getResult();

            $this->updateItems($type, $components);

            $data = array_merge($data, $this->getData($components));
        }

        $this->normalizer->normalize($memorial, $data, [$this->level]);

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

            if (!$componentConfig['enabled']) {
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

                    if (array_key_exists($product->getTag(), $cache[$cacheKey][$this->level])) {

                        /** @var Range $range */
                        $range = $cache[$cacheKey][$this->level][$product->getTag()];

                        $this->collection['components'][$componentType]['items'][$product->getId()]['ranges'][] = $range;
                        $this->collection['components'][$componentType]['items'][$product->getId()]['defaults'] = [
                            'cmv' => $range->getCostPrice()
                        ];
                    }
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
        foreach ($components as $component) {
            $this->collection['components'][$type]['items'][$component->getId()] = [
                'product' => $component
            ];
        }
    }

    /**
     * @param array $components
     * @return array
     */
    private function getData(array $components)
    {
        $data['tags'] = [];
        $data['codes'] = [];
        foreach ($components as $component) {
            $data['tags'][] = $component->getTag();
            $data['codes'][$component->getTag()] = $component->getCode();
        }

        return $data;
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
        if (!$this->managers[$id]) {
            $this->managers[$id] = $this->container->get(sprintf('%s_manager', $id));
        }

        return $this->managers[$id];
    }
}

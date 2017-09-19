<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Form\Admin\MemorialFilterType;
use AppBundle\Service\Pricing\RangeNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("memorials")
 */
class MemorialsController extends AdminController
{
    /**
     * @Route("/", name="memorials")
     */
    public function indexAction(Request $request)
    {
        $memorials = $this->manager('memorial')->findAll();

        return $this->render('admin/memorials/index.html.twig', [
            'memorials' => $memorials
        ]);
    }

    /**
     * @Route("/{id}/config", name="memorials_config")
     */
    public function configAction(Memorial $memorial, Request $request)
    {
        $rangeLevels = $this->manager('range')->distinct('level', ['memorial' => $memorial]);
        $accountLevels = $this->manager('account')->distinct('level');

        $levels = array_unique(array_merge($rangeLevels, $accountLevels));

        //dump($levels); die;

        $form = $this->createForm(MemorialFilterType::class, null, [
            'memorial' => $memorial,
            'levels' => $levels
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $criteria = [
                'status' => true,
                'available' => true
            ];

            $collection = [
                'components' => [
                    'module' => [
                        'enabled' => true,
                        'label' => 'Modules',
                        'items' => []
                    ],
                    'inverter' => [
                        'enabled' => true,
                        'label' => 'Inverters',
                        'items' => []
                    ]
                ]
            ];

            $modules = $this->manager('module')->findBy($criteria);
            $inverters = $this->manager('inverter')->findBy($criteria);
            $stringBoxes = $this->manager('string_box')->findBy($criteria);
            $structures = $this->manager('structure')->findBy($criteria);
            $varieties = $this->manager('variety')->findBy($criteria);

            foreach ($inverters as $inverter) {
                $collection['components']['inverter']['items'][$inverter->getId()] = [
                    'product' => $inverter
                ];
            }

            foreach ($modules as $module) {
                $collection['components']['module']['items'][$module->getId()] = [
                    'product' => $module
                ];
            }

            //dump($collection); die;

            $moduleCodes = $this->filterCodes($modules);
            $inverterCodes = $this->filterCodes($inverters);
            $stringBoxCodes = $this->filterCodes($stringBoxes);
            $structureCodes = $this->filterCodes($structures);
            $varietyCodes = $this->filterCodes($varieties);

            /** @var RangeNormalizer $normalizer */
            $normalizer = $this->get('range_normalizer');

            $data = $form->getData();
            //$codes = array_merge($moduleCodes, $inverterCodes, $stringBoxCodes, $structureCodes, $varietyCodes);
            $codes = array_merge($inverterCodes, $moduleCodes);
            $level = $data['level'];
            $levels = [$level];

            $start = microtime(true);

            $normalizer->normalize($memorial, $codes, $levels);

            $stop = microtime(true);

            $cache = $normalizer->getCache();
            $powers = $normalizer->getPowers();

            foreach ($collection['components'] as $componentType => $componentConfig) {

                $items = $componentConfig['items'];

                foreach ($items as $itemConfig) {

                    $product = $itemConfig['product'];

                    foreach ($powers as $power) {

                        list($initialPower, $finalPower) = $power;

                        $cacheKey = $normalizer->createCacheKey($initialPower, $finalPower);

                        $range = $cache[$cacheKey][$level][$product->getCode()];

                        $collection['components'][$componentType]['items'][$product->getId()]['ranges'][] = $range;
                    }
                }
            }

            //dump($collection); die;

            return $this->render('admin/memorials/debug.html.twig', [
                'collection' => $collection,
                'memorial' => $memorial,
                'powers' => $powers
            ]);

            return $this->render('admin/memorials/ranges.html.twig', [
                'collection' => $collection,
                'memorial' => $memorial,
                'powers' => $powers
            ]);
        }

        return $this->render('admin/memorials/admin.html.twig', [
            'memorial' => $memorial,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/update", name="memorials_product_update")
     */
    public function updateProductAction(Request $request)
    {
        $id = $request->request->get('id');
        $field = $request->request->get('field');
        $value = $request->request->get('value');
        $type = $request->request->get('type');

        $manager = $this->manager($type);
        $component = $manager->find($id);

        if($component){

            $accessor = PropertyAccess::createPropertyAccessor();

            $accessor->setValue($component, $field, $value);

            $manager->save($component);
        }

        return $this->json([
            'product' => [
                'type' => $type,
                'id' => $id,
                'field' => $field,
                'value' => $value
            ]
        ]);
    }

    /**
     * @Route("/ranges/update", name="memorials_range_update")
     */
    public function updateRangesAction(Request $request)
    {
        $manager = $this->manager('range');
        $data = $request->request->get('ranges');

        foreach ($data as $key => $config){

            $range = $manager->find($config['id']);

            if($range instanceof Range){

                $range
                    ->setCostPrice($config['cost'])
                    ->setMarkup($config['markup'])
                ;

                $manager->save($range, false);

                $data[$key]['price'] = $range->getPrice();
            }
        }

        $manager->getEntityManager()->flush();

        return $this->json([
            'ranges' => $data
        ]);
    }

    /**
     * @param Memorial $memorial
     * @param $level
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderRanges(Memorial $memorial, $level, array $components)
    {
        $rangeManager = $this->manager('range');

        $collection = [
            'ranges' => [],
            'components' => [
                'module' => [
                    'enabled' => in_array('module', $components),
                    'label' => 'Módulos',
                    'products' => []
                ],
                'inverter' => [
                    'enabled' => in_array('inverter', $components),
                    'label' => 'Inversores',
                    'products' => []
                ],
                'string_box' => [
                    'enabled' => in_array('string_box', $components),
                    'label' => 'String Box',
                    'products' => []
                ],
                'structure' => [
                    'enabled' => in_array('structure', $components),
                    'label' => 'Estruturas',
                    'products' => []
                ],
                'variety' => [
                    'enabled' => in_array('variety', $components),
                    'label' => 'Variedades',
                    'products' => []
                ]
            ]
        ];

        $createRange = function ($code, $start, $end) use ($memorial, $level, $rangeManager) {

            $range = new Range();

            $range
                ->setMemorial($memorial)
                ->setLevel($level)
                ->setInitialPower($start)
                ->setFinalPower($end)
                ->setCode($code)
                ->setPrice(0);

            $rangeManager->save($range, false);

            return $range;
        };

        $createOffset = function (Range $range) {
            return sprintf('%s-%s', (int)$range->getInitialPower(), (int)$range->getFinalPower());
        };

        $fetchComponent = function ($config) use ($rangeManager, $memorial, $level, $createOffset, $createRange, &$collection) {

            $type = $config['type'];

            $products = $this->manager($type)->findBy([
                'available' => true,
                'status' => true
            ]);

            /** @var ComponentTrait|ModuleInterface $product */
            foreach ($products as $product) {

                $code = $product->getCode();

                $ranges = $rangeManager->findBy([
                    'code' => $code,
                    'memorial' => $memorial,
                    'level' => $level
                ]);

                $offsets = array_map(function (Range $range) use ($createOffset) {
                    return $createOffset($range);
                }, $ranges);

                $ranges = array_combine($offsets, $ranges);

                /** @var Range $range */
                foreach ($ranges as $range) {
                    $offset = $createOffset($range);
                    if (!array_key_exists($offset, $collection['ranges'])) {
                        $collection['ranges'][$offset] = $range;
                    }
                }

                if (count($ranges) != count($collection['ranges'])) {
                    foreach ($collection['ranges'] as $offset => $range) {
                        if (!array_key_exists($offset, $ranges)) {
                            $lastRange = $createRange($code, $range->getInitialPower(), $range->getFinalPower());
                            $ranges[$offset] = $lastRange;
                        }
                    }
                }

                $collection['components'][$type]['products'][$code]['product'] = $product;
                $collection['components'][$type]['products'][$code]['ranges'] = $ranges;
            }
        };

        $firstComponent = null;
        $lastRange = null;
        foreach ($collection['components'] as $type => $config) {
            if ($config['enabled']) {

                $config['type'] = $type;

                if (!$firstComponent) {
                    $firstComponent = $config;
                }

                $fetchComponent($config);
            }
        }

        if ($firstComponent)
            $fetchComponent($firstComponent);

        if ($lastRange instanceof Range) {
            $rangeManager->save($lastRange);
        }

        //dump($collection); die;

        return $this->render('platform/memorials/ranges.html.twig', [
            'memorial' => $memorial,
            'collection' => $collection,
        ]);
    }

    /**
     * @param $type
     * @param $id
     * @return null|object
     */
    private function getComponent($type, $id)
    {
        return $this->manager($type)->find($id);
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
}

<?php

namespace AppBundle\Controller\Platform;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\ComponentTrait;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Form\Extra\MemorialFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/memorials")
 */
class MemorialsController extends AbstractController
{
    /**
     * @Route("/", name="platform_memorials_index")
     */
    public function indexAction(Request $request)
    {
        $memorials = $this->manager('memorial')->findBy([
            'status' => true
        ]);

        return $this->render('platform/memorials/index.html.twig', [
            'memorials' => $memorials,
        ]);
    }

    /**
     * @Route("/products", name="platform_products")
     */
    public function getProductsAction(Request $request)
    {
        $form = $this->createForm(MemorialFilterType::class, null, [
            'manager' => $this->manager('memorial')
        ]);

        $form->handleRequest($request);

        $products = null;
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid() && $data['level']) {

            $products = $this->renderRanges(
                $data['memorial'],
                $data['level'],
                $data['components']
            )->getContent();
        }

        return $this->render('platform/memorials/filter.html.twig', [
            'form' => $form->createView(),
            'products' => $products
        ]);
    }

    /**
     * @Route("/ranges/save", name="platform_range_save")
     */
    public function saveRangeAction(Request $request)
    {
        $data = $request->isMethod('post') ? $request->request->all() : $request->query->all();

        $id = (int)$data['id'];
        $manager = $this->manager('range');

        /** @var Range $range */
        $range = $id ? $manager->find($id) : $manager->create();
        unset($data['id']);

        $data['memorial'] = $this->manager('memorial')->find($data['memorial']);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $property => $value) {
            $accessor->setValue($range, $property, $value);
        }

        $manager->save($range);

        return $this->json([
            'range' => $range->toArray()
        ]);
    }

    /**
     * @Route("/", name="platform_memorials")
     */
    public function getMemorialsAction()
    {
        $memorials = $this->manager('memorial')->findBy([]);

        return $this->render('platform/memorials/memorials.html.twig', [
            'memorials' => $memorials
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

        $createRange = function($code, $start, $end) use($memorial, $level, $rangeManager){

            $range = new Range();

            $range
                ->setMemorial($memorial)
                ->setLevel($level)
                ->setInitialPower($start)
                ->setFinalPower($end)
                ->setCode($code)
                ->setPrice(0)
            ;

            $rangeManager->save($range, false);

            return $range;
        };

        $createOffset = function(Range $range){
            return sprintf('%s-%s', (int)$range->getInitialPower(), (int)$range->getFinalPower());
        };

        $lastRange = null;
        foreach ($collection['components'] as $type => $config) {

            if($config['enabled']) {

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
            }
        }

        if($lastRange instanceof Range){
            $rangeManager->save($lastRange);
        }

        return $this->render('platform/memorials/ranges.html.twig', [
            'memorial' => $memorial,
            'collection' => $collection,
        ]);
    }
}
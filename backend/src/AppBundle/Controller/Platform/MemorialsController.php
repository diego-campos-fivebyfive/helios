<?php

namespace AppBundle\Controller\Platform;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\ComponentTrait;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Form\Extra\MemorialFilterType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function indexAction()
    {
        return $this->render('platform/memorials/index.html.twig');
    }

    /**
     * @Route("/import", name="platform_memorials_import")
     */
    public function importAction(Request $request)
    {
        $memorial = null;

        if($request->isMethod('post')) {

            $file = $request->files->get('memorial');

            if ($file instanceof UploadedFile) {

                $name = 'memorial_' . uniqid(md5(time())) . '.json';

                $kernel = $this->get('kernel');
                $dir = $this->get('kernel')->getRootDir() . '/cache/' . $kernel->getEnvironment() . '/';

                try {
                    $file = $file->move($dir, $name);
                }catch (\Exception $e){
                    die($e->getMessage());
                }

                $data = json_decode(file_get_contents($file->getRealPath()), true);

                $memorialManager = $this->manager('memorial');
                $rangeManager = $this->manager('range');
                $accessor = PropertyAccess::createPropertyAccessor();

                $mapping = [
                    'BLACK' => 'black',
                    'PLATINUM'  => 'platinum',
                    'PREMIUM' => 'premium',
                    'PARCEIRO'  => 'partner',
                    'PROMOCIONAL' => 'promotional'
                ];

                $memorialInfo = $data['Dados'];
                $version = $memorialInfo['Versao'];
                $memorial = $memorialManager->findOneBy(['version' => $version]);

                if($memorial instanceof Memorial && null != $request->request->get('remove')){
                    $memorialManager->delete($memorial);
                    $memorial = null;
                }

                if(!$memorial){

                    /** @var Memorial $memorial */
                    $memorial = $memorialManager->create();

                    $memorial
                        ->setIsquikId($memorialInfo['IdTabelaBase'])
                        ->setStartAt(new \DateTime())
                        ->setStatus(true)
                        ->setVersion($version)
                    ;

                    foreach ($memorialInfo['Produtos'] as $data) {

                        $code = $data['Codigo'];

                        foreach ($data['Faixas'] as $zoneInfo) {

                            $initialPower = $zoneInfo['De'];
                            $finalPower = $zoneInfo['Ate'];

                            foreach ($zoneInfo['Niveis'] as $levelInfo) {

                                $properties = [
                                    'memorial' => $memorial,
                                    'code' => $code,
                                    'initialPower' => $initialPower,
                                    'finalPower' => $finalPower,
                                    'level' => $mapping[$levelInfo['Descricao']],
                                    'price' => $levelInfo['PrecoVenda']
                                ];

                                /** @var Range $range */
                                $range = $rangeManager->create();

                                foreach ($properties as $property => $value) {
                                    $accessor->setValue($range, $property, $value);
                                }
                            }
                        }
                    }

                    $memorialManager->save($memorial);

                    return $this->redirectToRoute('platform_memorials_index', [
                        'memorial' => $memorial->getId()
                    ]);
                }
            }
        }

        return $this->render('platform/memorials/import.html.twig', [
            'memorial' => $memorial
        ]);
    }

    /**
     * @Route("/products", name="platform_products")
     */
    public function getProductsAction(Request $request)
    {
        $data = [];

        if(0 != $id = $request->query->getInt('memorial')){
            if(null != $memorial = $this->manager('memorial')->find($id)){
                $data['memorial'] = $memorial;
            }
        }

        $form = $this->createForm(MemorialFilterType::class, $data, [
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

        $fetchComponent = function($config) use($rangeManager, $memorial, $level, $createOffset, $createRange, &$collection){

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
            if($config['enabled']){

                $config['type'] = $type;

                if(!$firstComponent){
                    $firstComponent = $config;
                }

                $fetchComponent($config);
            }
        }

        if($firstComponent)
            $fetchComponent($firstComponent);

        if($lastRange instanceof Range){
            $rangeManager->save($lastRange);
        }

        return $this->render('platform/memorials/ranges.html.twig', [
            'memorial' => $memorial,
            'collection' => $collection,
        ]);
    }
}

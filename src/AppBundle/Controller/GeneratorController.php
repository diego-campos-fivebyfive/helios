<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Service\ProjectGenerator\Combiner;
use AppBundle\Service\ProjectGenerator\InverterCombiner;
use AppBundle\Service\ProjectGenerator\InverterLoader;
use AppBundle\Service\ProjectGenerator\MakerDetector;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("generator")
 */
class GeneratorController extends AbstractController
{
    /**
     * @Route("/", name="generator_xhr")
     */
    public function xhrAction(Request $request)
    {
        $json = '{}';
        $components = (json_decode($json, true));
        $makerManager = $this->manager('maker');
        $inverterManager = $this->manager('inverter');

        $makersNF = '';
        foreach ($components['app_component_inverter'] as $data) {

            $maker = $makerManager->find($data['maker']);

            /** @var InverterInterface $inverter */
            $inverter = $inverterManager->create();

            $inverter
                ->setCode(strtoupper(uniqid(time())))
                ->setModel($data['model'])
                ->setMaxDcPower($data['max_dc_power'])
                ->setMaxDcVoltage($data['max_dc_voltage'])
                ->setNominalPower($data['nominal_power'])
                ->setMpptMaxDcCurrent($data['mppt_max_dc_current'])
                ->setMaxEfficiency($data['max_efficiency'])
                ->setMpptMax($data['mppt_max'])
                ->setMpptMin($data['mppt_min'])
                ->setMpptNumber($data['mppt_number'])
                ->setMpptConnections($data['number_con_mppt'])
                ->setConnectionType($data['con_type'])
                ->setMpptParallel($data['mppt_parallel'])
                ->setInProtection($data['in_protections'])
                ->setPhases($data['phase_number'])
                ->setMaker($maker)
            ;

            //$inverterManager->save($inverter);
            //dump($inverter->getId());
        }

        die;
    }

    /**
     * @Route("/inverter-combiner", name="inverter_combiner")
     */
    public function testInverterCombinerAction()
    {
        // MAKER DETECTOR
        /*$power = 500;
        /** @var \AppBundle\Manager\InverterManager $manager *
        $manager = $this->manager('inverter');

        $makerDetector = new MakerDetector($manager);

        $makers = $makerDetector->fromPower($power);

        dump($makers); die;*/

        /// INVERTER LOADER
        $sungrow = 61208;
        $canadian = 60694;
        $fronius = 60630;
        $abb = 60627;

        $power = 0.5;
        $maker = $canadian;
        $min = $power * 0.75;

        $manager = $this->manager('inverter');

        $loader = new InverterLoader($manager);

        $inverters = $loader->load($power, $maker);

        // INVERTER COMBINER
        /*$inverters = [
            $manager->find(6435),
            $manager->find(6434)
        ];*/
        //dump($inverters); die;

        InverterCombiner::combine($inverters, $min);

        dump($inverters); die;
    }

    /**
     * @Route("/ranges")
     */
    public function rangesAction()
    {
        $getCode = function () {
            return strtoupper(substr(md5(uniqid(time())), 0, 10));
        };

        $codes = [];

        // MODULES
        $moduleManager = $this->manager('module');
        $modules = $moduleManager->findAll();

        foreach ($modules as $module) {
            if (null == $code = $module->getCode()) {
                $code = $getCode();
                $module->setCode($code);
                $moduleManager->save($module);
            }
            $codes[] = $code;
        }

        // INVERTERS
        $inverterManager = $this->manager('inverter');
        $inverters = $inverterManager->findAll();

        foreach ($inverters as $inverter) {
            if (null == $code = $inverter->getCode()) {
                $code = $getCode();
                $inverter->setCode($code);
                $inverterManager->save($inverter);
            }
            $codes[] = $code;
        }

        // STRUCTURES
        $structureManager = $this->manager('structure');
        $structures = $structureManager->findAll();
        foreach ($structures as $structure) {
            if (null == $code = $structure->getCode()) {
                $code = $getCode();
                $structure->setCode($code);
                $structureManager->save($structure);
            }
            $codes[] = $code;
        }

        // STRING_BOXES
        $stringBoxManager = $this->manager('string_box');
        $stringBoxes = $stringBoxManager->findAll();
        foreach ($stringBoxes as $stringBox) {
            if (null == $code = $stringBox->getCode()) {
                $code = $getCode();
                $stringBox->setCode($code);
                $stringBoxManager->save($stringBox);
            }
            $codes[] = $code;
        }

        // VARIETIES
        $varietyManager = $this->manager('string_box');
        $varieties = $varietyManager->findAll();
        foreach ($varieties as $variety) {
            if (null == $code = $variety->getCode()) {
                $code = $getCode();
                $variety->setCode($code);
                $varietyManager->save($variety);
            }
            $codes[] = $code;
        }

        // MEMORIAL + RANGES
        $memorialManager = $this->manager('memorial');
        $memorial = $memorialManager->findOneBy(['status' => 1]);

        if (!$memorial) {
            $memorial = $memorialManager->create();

            $memorial
                ->setStartAt(new \DateTime('-15 days'))
                ->setEndAt(new \DateTime('15 days'))
                ->setStatus(1);

            $memorialManager->save($memorial);
        }

        $rangeManager = $this->manager('range');

        foreach ($codes as $code) {

            if(null == $range = $rangeManager->findOneBy(['code' => $code, 'memorial' => $memorial])){

                $range = $rangeManager->create();
                $range
                    ->setMemorial($memorial)
                    ->setLevel('platinum')
                    ->setInitialPower(20)
                    ->setFinalPower(500)
                    ->setMarkup(.1)
                    ->setCode($code)
                    ->setPrice(1000);

                $memorial->addRange($range);

                $rangeManager->save($range);
            }
        }

        $memorialManager->save($memorial);
        dump($memorial);
        die;
    }

    /**
     * @Route("/kit", name="generate_kit")
     */
    public function indexAction(Request $request)
    {
        $project = $this->manager('project')->find(207);

        $area = $project->getAreas()->first();

        dump($area->getMetadata());
        die;


        for ($i = 1; $i <= 1; $i++) {

            $power = 15 * rand($i, 3);

            //$power = $this->get('power_estimator')->estimate($project->getInfPower(), $project->getLatitude(), $project->getLongitude());

            /** @var \AppBundle\Entity\Component\Module $mod */
            $mod = $this->manager('module')->find(32433);

            /** @var \AppBundle\Entity\Component\MakerInterface $maker */
            $maker = $this->manager('maker')->find(60627);
            $roofType = 1;
            $position = 0;

            /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $generator */
            $generator = $this->get('project_generator');

            /** @var ProjectInterface $project */
            $project = $this->manager('project')->create();

            $project
                ->setStructureType(ProjectInterface::STRUCTURE_SICES)
                ->setRoofType($roofType);

            $project = $generator
                ->project($project)
                ->power($power)
                ->module($mod, $position)
                ->maker($maker)
                ->generate();

            dump('Projeto Gerado: ' . $project->getId());
        }

        die;

        $strCalculator = $this->get('structure_calculator');
        $prof = $strCalculator->findStructure(['type' => 'perfil', 'subtype' => 'roman'], false);

        $profiles = [];
        foreach ($prof as $pf) {
            $profiles[] = Structure\Profile::create($pf['code'], $pf['size']);
        }

        $itemEntities = $strCalculator->loadItems();

        $items = [];
        foreach ($itemEntities as $type => $itemEntity) {
            $items[$type] = Structure\Item::create($type, $itemEntity['size']);
        }

        $project = Project::create(
            $roofType,
            $inverters,
            [$module],
            $profiles,
            $items
        );

        Combiner::combine($project);
        Structure::calculate($project);

        // StringBox
        $loader = new StringBoxLoader($this->manager('string_box'));

        $calculator = new Calculator();
        $calculator->setLoader($loader);
        $calculator->calculate($project);

        dump($project);
        die;

        //$this->previewPower(1000, -15.79, -47.88);
        //$this->generateJson();
        //$this->calculateStructure();
        //$this->calculateStringBoxes();

        $member = $this->member();
        $contacts = $member->getContacts();
        $customer = $contacts->get(rand(0, $contacts->count() - 1));
        $kwh = 500;

        $latitude = -15.79;
        $longitude = -47.88;

        $modId = 32433;
        /** @var ModuleInterface $module */
        $module = $this->manager('module')->find($modId);
        //$pot = $this->previewPower(10000, $data['latitude'], $data['longitude']);
        $power = $this->get('power_estimator')->estimate(
            $kwh,
            $latitude,
            $longitude
        );

        $maker = $this->manager('maker')->findOneBy(['context' => 'component_inverter']);

        $inverters = $this->get('inverter_combinator')->combine($module, $power, $maker);

        $stringBoxCalculator = $this->get('string_box_calculator');

        $stringBoxes = $stringBoxCalculator->calculate($inverters);


        dump($stringBoxes);
        die;
    }

    private function calculateStringBoxes(array $inverters)
    {

    }

    private function generateJson()
    {
        $data = [
            'version' => '0000001',
            'start_at' => (new \DateTime)->format('Y-m-d'),
            'end_at' => (new \DateTime('+1 month'))->format('Y-m-d'),
            'products' => [
                [
                    'code' => 'ABC',
                    'description' => 'The product A',
                    'family' => 'inverter',
                    'markups' => [
                        'platinum' => [
                            [
                                'start' => 1000,
                                'end' => 3000,
                                'markup' => 0.1
                            ],
                            [
                                'start' => 30001,
                                'end' => 5000,
                                'markup' => 0.15
                            ]
                        ],
                        'gold' => [
                            [
                                'start' => 1000,
                                'end' => 3000,
                                'markup' => 0.15
                            ],
                            [
                                'start' => 30001,
                                'end' => 5000,
                                'markup' => 0.2
                            ]
                        ]
                    ]
                ],
                [
                    'code' => 'CDE',
                    'description' => 'The product B',
                    'family' => 'structure',
                    'markups' => [
                        'platinum' => [
                            [
                                'start' => 1000,
                                'end' => 3000,
                                'markup' => 0.1
                            ],
                            [
                                'start' => 30001,
                                'end' => 5000,
                                'markup' => 0.2
                            ]
                        ],
                        'gold' => [
                            [
                                'start' => 1000,
                                'end' => 3000,
                                'markup' => 0.2
                            ],
                            [
                                'start' => 30001,
                                'end' => 5000,
                                'markup' => 0.3
                            ]
                        ]
                    ]
                ]
            ]
        ];


        dump(json_encode($data));
        die;
    }

}
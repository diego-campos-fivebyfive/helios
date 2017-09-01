<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\ProjectGenerator\Combiner;
use AppBundle\Service\ProjectGenerator\InverterCombiner;
use AppBundle\Service\ProjectGenerator\InverterLoader;
use AppBundle\Service\ProjectGenerator\MakerDetector;
use AppBundle\Service\RegisterHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/debug/generator")
 */
class GeneratorController extends AbstractController
{
    /**
     * @Route("/accounts", name="generator_accounts")
     */
    public function accountsAction()
    {
        die('LOCKED');

        $manager = $this->manager('account');
        $userManager = $this->get('fos_user.user_manager');
        $categoryManager = $this->manager('category');

        //dump($manager->getConnection()->getHost()); die;

        /** @var RegisterHelper $helper */
        $helper = $this->get('app.register_helper');

        for($i = 1; $i <= 20; $i++){

            /** @var AccountInterface|BusinessInterface $account */
            $account = $manager->create();

            $email = sprintf('sices%d@sices.com.br', $i);
            $password = 'sices';

            $account
                ->setContext('account')
                ->setEmail($email)
                ->setFirstname(sprintf('Sices %s', $i))
                ->setLastname('Testes')
                ->setDocument(sprintf("%02d.%03d.%03d/0001-%02d", $i, $i, $i, $i))
                ->setEmail(sprintf('testes%d@sices.com.br', $i))
                ->setPhone(sprintf('(%02d) %05d-%04d', $i, $i, $i))
                ->setCountry('Brazil')
                ->setState('SP')
                ->setCity('Itapevi')
                ->setDistrict('Industrial')
                ->setStreet('Av. Presidente Prudente')
                ->setNumber($i * 75)
                ->setExtraDocument(sprintf('%03d.%03d.%02d', $i, $i, $i))
                ->setLevel('default')
            ;

            /** @var UserInterface $user */
            $user = $userManager->createUser();

            $user
                ->setUsername($email)
                ->setEmail($email)
                ->setPlainPassword($password)
                ->setEnabled(true)
                ->addRole(UserInterface::ROLE_OWNER)
                ->addRole(UserInterface::ROLE_OWNER_MASTER)
            ;

            $member = $manager->create();

            $member->setFirstname(sprintf('Usuário %d', $i))
                ->setEmail($email)
                ->setContext(BusinessInterface::CONTEXT_MEMBER)
                ->setUser($user)
                ->setAccount($account)
            ;

            $manager->save($account);

            $category = $categoryManager->findOneBy([
                'context' => 'contact_category',
                'account' => $account
            ]);

            /** @var BusinessInterface $contact */
            $contact = $manager->create();

            $contact
                ->setContext('person')
                ->setFirstname(sprintf('Pessoa %s', $i))
                ->setPhone(sprintf('(%02d) %05d-%04d', $i, $i, $i))
                ->setDocument(sprintf("%03d.%03d.%03d-%02d", $i, $i, $i, $i))
                ->setCategory($category)
                ->setEmail(sprintf('contato%d@sices.com.br', $i))
                ->setMember($member)
            ;

            $manager->save($contact);
        }

        die('OK');
    }

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
     * @Route("/project-test", name="project_test_generator")
     */
    public function testInverterCombinerAction()
    {
        /** @var ProjectInterface $project */
        $project = $this->manager('project')->create();

        /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $generator */
        $generator = $this->get('project_generator');

        $defaults = $generator->loadDefaults([
           //'latitude' => -25.384,
            //'longitude' => -51.455,
            'source' => 'power',
            'power' => 100,
            'grid_voltage' => '127/220',
            'grid_phase_number' => 'Triphasic',
            'inverter_maker' => 60627
        ]);

        $project->setDefaults($defaults);
        $generator->autoSave(false)->project($project)->generate();

        dump($project->getProjectModules()->first()->getGroups()); die;
    }

    /**
     * @Route("/import-transformers", name="import_transformers")
     */
    public function importTransformersAction()
    {
        $filename = $this->get('kernel')->getRootDir() . '/../web/transformers.csv';

        $data = explode("\n", file_get_contents($filename));
        array_shift($data);

        $manager = $this->manager('variety');

        foreach ($data as $info){
            $props = explode(';', $info);

            if(4 == count($props)) {
                /** @var VarietyInterface $variety */
                $variety = $manager->create();
                $variety
                    ->setType(VarietyInterface::TYPE_TRANSFORMER)
                    ->setCode($props[0])
                    ->setDescription($props[1])
                    ->setPower((int)$props[2])
                ;

                //$manager->save($variety);
            }
        }

        die;
    }

    /**
     * @Route("/codes", name="debug_codes")
     */
    public function codesAction(Request $request)
    {
        die('RUN_ONLY_LOCAL');

        $data = json_decode(file_get_contents($this->get('kernel')->getRootDir() . './../web/uploads/codes.json'), true);

        foreach ($data as $id => $codes){

            $manager = $this->manager($id);

            //dump($manager->getConnection()->getHost()); die;

            $alias = (substr($id, 0, 1));

            $entities = $manager
                ->createQueryBuilder()
                ->where(sprintf('%s.code is not null', $alias))
                ->getQuery()
                ->setMaxResults(count($codes))
                ->getResult()
            ;

            /** @var \AppBundle\Entity\Component\InverterInterface $entity */
            foreach ($entities as $key => $entity) {
                if($entity->getCode() != $codes[$key]) {
                    $entity->setCode($codes[$key]);
                    $manager->save($entity, $key == count($codes) - 1);
                }
            }
        }

        dump($data); die;
    }

    /**
     * @Route("/ranges")
     */
    public function rangesAction(Request $request)
    {
        $getCode = function () {
            return strtoupper(substr(md5(uniqid(time())), 0, 10));
        };

        $codes = [];

        $prices = [
            'platinum' => ['modules' => 100,
                'inverters' => 1000,
                'structures' => 100,
                'string_boxes' => 1000,
                'varieties' => 10
            ],
            'promotional' => ['modules' => 90,
                'inverters' => 900,
                'structures' => 90,
                'string_boxes' => 900,
                'varieties' => 9
            ],
        ];

        // MODULES
        $moduleManager = $this->manager('module');
        $modules = $moduleManager->findAll();

        foreach ($modules as $module) {
            if (null == $code = $module->getCode()) {
                $code = $getCode();
                $module->setCode($code);
                $moduleManager->save($module);
            }
            $codes['modules'][] = $code;
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
            $codes['inverters'][] = $code;
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
            $codes['structures'][] = $code;
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
            $codes['string_boxes'][] = $code;
        }

        // VARIETIES
        $varietyManager = $this->manager('variety');
        $varieties = $varietyManager->findAll();
        foreach ($varieties as $variety) {
            if (null == $code = $variety->getCode()) {
                $code = $getCode();
                $variety->setCode($code);
                $varietyManager->save($variety);
            }
            $codes['varieties'][] = $code;
        }

        // MEMORIAL + RANGES
        $memorialManager = $this->manager('memorial');
        $memorial = $this->get('memorial_loader')->load();
        if (!$memorial) {
            $memorial = $memorialManager->create();

            $memorial
                ->setStartAt(new \DateTime('-15 days'))
                ->setEndAt(new \DateTime('15 days'))
                ->setStatus(1);

            $memorialManager->save($memorial);
        }

        /*dump($codes);
        dump($prices);
        die;*/

        $level = $request->query->get('level', 'platinum');
        $rangeManager = $this->manager('range');

        foreach ($codes as $family => $data) {

            foreach($data as $code) {

                if (null == $range = $rangeManager->findOneBy(['code' => $code, 'memorial' => $memorial, 'level' => $level])) {

                    $range = $rangeManager->create();
                    $range
                        ->setMemorial($memorial)
                        ->setLevel($level)
                        ->setInitialPower(0)
                        ->setFinalPower(500)
                        ->setCode($code)
                        ->setPrice($prices[$level][$family]);

                    $memorial->addRange($range);

                    $rangeManager->save($range);
                }
            }
        }

        $memorialManager->save($memorial);

        print_r($codes); die;
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
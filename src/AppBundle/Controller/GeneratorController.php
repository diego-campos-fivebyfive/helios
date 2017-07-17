<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\StructureInterface;
use AppBundle\Form\Extra\KitGeneratorType;
use AppBundle\Util\KitGenerator\InverterCombiner\Module;
use AppBundle\Util\KitGenerator\StructureCalculator;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("generator")
 */
class GeneratorController extends AbstractController
{
    /**
     * @Route("/xhr", name="generator_xhr")
     */
    public function xhrAction(Request $request)
    {
        return $this->json();
    }

    /**
     * @Route("/kit", name="generate_kit")
     */
    public function indexAction(Request $request)
    {
        //$this->previewPower(1000, -15.79, -47.88);
        //$this->generateJson();
        $this->calculateStructure();

        $generator = new ProjectGenerator($this->container);

        $member = $this->member();
        $contacts = $member->getContacts();
        $customer = $contacts->get(rand(0, $contacts->count() - 1));

        $data = [
            'member' => $member,
            'customer' => $customer,
            'latitude' => random_int(10, 100) * 775.25,
            'longitude' => random_int(10, 100) * 775.25,
            'number' => rand(500, 1000),
            'address' => md5(uniqid(time())),
            'inverters' => [
                5861 => 1
            ],
            'modules' => [
                32432 => 24
            ],
            'structures' => [
                1 => 28,
                13 => 158,
                15 => 112,
                14 => 122
            ],
            'extras' => [
                1 => 5,
                2 => 3,
                3 => 1
            ]
        ];

        $project = $generator->fromArray($data);
        //dump($project); die;

        //ini_set('max_execution_time', '120');

        $this->createProject();

        $data = [
            'latitude' => -15.79,
            'longitude' => -47.88
        ];

        $modId = 32433;
        /** @var ModuleInterface $module */
        $module = $this->manager('module')->find($modId);
        $pot = $this->previewPower(10000, $data['latitude'], $data['longitude']);

        $data = $this->calculateInverter($pot, $module);

        dump($data); die;

        // Makers
       /* $makers = $this->manager('maker')->findBy([
            'context' => 'component_inverter'
        ], null, 1);*/

        /*$inverters = [];
        foreach ($makers as $key => $maker) {
            dump($key);
            $inverters[] = $this->calculateInverter($pot, $module, $maker);
        }*/

        $form = $this->createForm(KitGeneratorType::class, $data);

        $form->handleRequest($request);

        $est = [];
        $inv = [];
        $pot_kit = null;
        $pot = null;
        $module = null;
        $mod_qte = 0;

        if ($request->isMethod('post') && $form->isValid()) {

            $data = $form->getData();

            $kwh = (float)$data['kwh'];
            $latitude = (float)$data['latitude'];
            $longitude = (float)$data['longitude'];

            $telhados = [
                'Telhas Romanas e Americanas' => 0,
                'Telhas de Ficrocimento' => 1,
                'Laje Plana' => 2,
                'Chapa Metálica' => 3,
                'Chapa Metálica com Perfil de 0,5m' => 4
            ];

            $tipo_telhado = $telhados[$data['tipo_telhado']];

            $maker_est = $data['maker_est'] == 'K2 System' ? 2 : 1;

            /** @var \AppBundle\Entity\Component\Module $mod */
            $mod = $this->getModuleManager()->find(32433);

            dump($mod);

            $module = new Module();
            $module
                ->setId($mod->getId())
                ->setModel($mod->getModel())
                ->setLength(1.65)
                ->setWidth(.992)
                ->setCellNumber($mod->getCellNumber())
                ->setOpenCircuitVoltage($mod->getOpenCircuitVoltage())
                ->setVoltageMaxPower($mod->getVoltageMaxPower())
                ->setTempCoefficientVoc($mod->getTempCoefficientOpenCircuitVoltage())
                ->setMaxPower($mod->getMaxPower())
                ->setShortCircuitCurrent($mod->getShortCircuitCurrent())
            ;

            //dump($module); die;
            $mod_qte = 0;
            $inv_maker = 60630;

            $pot = $this->prev_pot($kwh, $latitude, $longitude);
            $inv = $this->calc_inv($pot, $module, $inv_maker);

            while ($inv == false) {
                $kwh += 10;
                $pot = $this->prev_pot($kwh, $latitude, $longitude);
                $inv = $this->calc_inv($pot, $module, $inv_maker);
            }

            for ($i = 0; $i < count($inv); $i++) {
                $id = $inv[$i]["inv_id"];
                $s = $inv[$i]["ser"];
                $p = $inv[$i]["par"];
                $qte = $inv[$i]["qte"];
                $mod_qte += $s * $p * $qte;
            }

            $est = $this->calc_est($maker_est, $mod_qte, $tipo_telhado, 0, $module);

            $pot_kit = $mod_qte * $module->getMaxPower() / 1000;

            //dump($est); die;
        }

        return $this->render('project.generator', [
            'form' => $form->createView(),
            'pot' => $pot,
            'inv' => $inv,
            'est' => $est,
            'mod' => $module,
            'pot_kit' => $pot_kit,
            'mod_qte' => $mod_qte
        ]);
    }

    private function createProject()
    {
        $kwh        = 100000;
        $latitude   = -15.79;
        $longitude  = -47.88;

        /** @var ModuleInterface $module */
        $module = $this->manager('module')->find(32433);

        $power = $this->get('power_estimator')->estimate($kwh, $latitude, $longitude);

        $inverterCombinator = $this->get('inverter_combinator');

        $inverters = $inverterCombinator->combine($module, $power, 60627);

        /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $projectGenerator */
        $projectGenerator = $this->get('project_generator');

        $project = $projectGenerator->fromCombination([
            'inverters' => $inverters,
            'module' => $module,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);

        dump($project); die;
    }

    private function calculateStructure()
    {
        //$structureManager = $this->manager('structure');

        /** @var \AppBundle\Entity\Component\ProjectInterface $project */
        $project = $this->manager('project')->find(141);

        //$manipulator = $this->get('project_manipulator');
        //$manipulator->generateAreas($project);

        dump($project->getChecklist()); die;

        die;

        dump(abs($project->getLatitude())); die;

        dump($project->getAreas()->count()); die;

        /*dump($project->getProjectModules()->toArray());

        $countModules = [];
        /** @var \AppBundle\Entity\Component\ProjectAreaInterface $area *
        foreach($project->getAreas() as $area){
            if(null != $projectModule = $area->getProjectModule()) {
                $module = $projectModule->getModule();

                if(!array_key_exists($module->getId(), $countModules))
                    $countModules[$module->getId()] = 0;

                $countModules[$module->getId()] += $area->countModules();
            }
        }

        foreach($project->getProjectModules() as $projectModule){
            $projectModule->setQuantity(
                $countModules[$projectModule->getModule()->getId()]
            );
        }

        $this->manager('project')->save($project);

        dump($countModules);
        dump($project->getProjectModules()->toArray()); die;*/

        /** @var ModuleInterface $module */
        $module = $project->getProjectModules()->first()->getModule();

        /** @var \AppBundle\Service\StructureCalculator\StructureCalculator $structureCalculator */
        $structureCalculator = $this->get('structure_calculator');

        $structureCalculator->calculate($project);

        dump($project); die;

        //dump($project->getProjectModules()->first()->getQuantity()); die;

        $profiles = [];
        /** @var StructureInterface $profile */
        foreach($structureManager->findBy(['type' => 'perfil', 'subtype' => 'roman']) as $profile){
            $profiles[] = [
                'id' => $profile->getId(),
                'code' => $profile->getCode(),
                'size' => $profile->getSize(),
                'quantity' => 0,
                'entity' => $profile
            ];
        }

        $data = [
            //'roof_type' => 1,
            StructureCalculator::ROOF => StructureCalculator::ROOF_ROMAN_AMERICAN,
            StructureCalculator::MODULE => [
                'cell_number' => $module->getCellNumber(),
                'length' => 1.65,   //$module->getLength(),
                'width' => 0.992,   //$module->getWidth(),
                'quantity' => 140,
                'position' => StructureCalculator::POSITION_VERTICAL
            ],
            StructureCalculator::PROFILES => $profiles
        ];

        $findStructure = function(array $criteria) use($structureManager){

            $structure = $structureManager->findOneBy($criteria);

            return [
                'id' => $structure->getId(),
                'code' => $structure->getCode(),
                'description' => $structure->getDescription(),
                'size' => $structure->getSize(),
                'quantity' => 0,
                'entity' => $structure
            ];
        };

        $mappingCriteria = [
            StructureCalculator::TERMINAL_FINAL => ['type' => 'terminal', 'subtype' => 'final'],
            StructureCalculator::TERMINAL_INTERMEDIARY => ['type' => 'terminal', 'subtype' => 'intermediario'],
            StructureCalculator::FIXER_BOLT => ['type' => 'fixador', 'subtype' => 'parafuso'],
            StructureCalculator::FIXER_NUT => ['type' => 'fixador', 'subtype' => 'porca'],
            StructureCalculator::BASE_HOOK => ['type' => 'base', 'subtype' => 'gancho'],
            StructureCalculator::BASE_FRICTION_TAPE => ['type' => 'base', 'subtype' => 'fita'],
            StructureCalculator::BASE_SPEED_CLIP => ['type' => 'base', 'subtype' => 'speedclip'],
            StructureCalculator::PROFILE_MIDDLE => ['type' => 'perfil', 'subtype' => 'meio_metro'],
            StructureCalculator::JUNCTION => ['type' => 'juncao'],
            StructureCalculator::BASE_SCREW_FRAME => ['type' => 'base', 'subtype' => 'parafuso_estrutural'],
            StructureCalculator::BASE_SCREW_AUTO => ['type' => 'base', 'subtype' => 'parafuso_autoperfurante'],
            StructureCalculator::BASE_TRIANGLE_VERTICAL => ['type' => 'base', 'subtype' => 'triangulo_vertical'],
            StructureCalculator::BASE_TRIANGLE_HORIZONTAL => ['type' => 'base', 'subtype' => 'triangulo_horizontal']
        ];

        foreach ($mappingCriteria as $field => $criteria){
            $data['ITEMS'][$field] = $findStructure($criteria);
        }

        StructureCalculator::calculate($data);

        $addStructure = function(ProjectInterface $project, StructureInterface $structure, $quantity){

            $projectStructure = new ProjectStructure();

            $projectStructure
                ->setProject($project)
                ->setStructure($structure)
                ->setQuantity($quantity)
            ;
        };

        foreach($data[StructureCalculator::ITEMS] as $item){
            $addStructure($project, $item['entity'], $item['quantity']);
        }

        foreach ($data[StructureCalculator::PROFILES] as $profile){
            $addStructure($project, $profile['entity'], $profile['quantity']);
        }

        dump($project); die;

        dump($data);

        die;

        /*$data = [
            //'roof_type' => 1,
            StructureCalculator::ROOF => StructureCalculator::ROOF_ROMAN_AMERICAN,
            StructureCalculator::MODULE => [
                'cell_number' => $module->getCellNumber(),
                'length' => 1.65,   //$module->getLength(),
                'width' => 0.992,   //$module->getWidth(),
                'quantity' => 140,
                'position' => StructureCalculator::POSITION_VERTICAL
            ],
            StructureCalculator::PROFILES => $profiles,
            StructureCalculator::PROFILE_MIDDLE => ['id' => $profileMiddle->getid(), 'quantity' => 0],
            StructureCalculator::TERMINAL_FINAL => ['id' => $terminalFinal->getId(),'code' => $terminalFinal->getCode(), 'size' => $terminalFinal->getSize(), 'quantity' => 0],
            StructureCalculator::TERMINAL_INTERMEDIARY => ['id' => $terminalMiddle->getId(), 'code' => $terminalMiddle->getCode(), 'size' => $terminalMiddle->getSize(), 'quantity' => 0],
            StructureCalculator::FIXER_BOLT => ['id' => $fixerScrew->getId(),'code' => $fixerScrew->getCode(), 'quantity' => 0],
            StructureCalculator::FIXER_NUT => ['id' => $fixerNut->getId(),'code' => $fixerNut->getCode(), 'quantity' => 0],
            StructureCalculator::BASE_HOOK => ['id' => $baseHook->getId(),'code' => $baseHook->getCode(), 'quantity' => 0],
            StructureCalculator::BASE_FRICTION_TAPE => ['id' => $baseBand->getId(),'code' => $baseBand->getCode(), 'quantity' => 0],
            StructureCalculator::BASE_SPEED_CLIP => ['id' => $baseSpeedClip->getId(),'code' => $baseSpeedClip->getCode(), 'quantity' => 0],
            StructureCalculator::JUNCTION => ['id' => $junction->getId(), 'quantity' => 0],
            StructureCalculator::BASE_SCREW_FRAME => ['id' => $baseScrewStr->getId()],
            StructureCalculator::BASE_TRIANGLE_VERTICAL => ['id' => $baseTriangleVertical->getId()],
            StructureCalculator::BASE_TRIANGLE_HORIZONTAL => ['id' => $baseTriangleHorizontal->getId()],
            StructureCalculator::BASE_SCREW_AUTO => ['id' => $baseScrewAuto->getId()]
        ];*/

        StructureCalculator::calculate($data);

        dump($data); die;

        $project = $this->manager('project')->find(119);

        $structureCalculator = $this->get('structure_calculator');
        $structureCalculator->calculate($project);

        dump($project); die;

        dump($this->manager('structure')->findAll()); die;

        $qb = $this->manager('structure')
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(Structure::class, 's')
            ->where('s.type = :type')
            ->andWhere('s.type = :subtype')
            ->setParameters([
                'type' => 'perfil',
                'subtype' => 'roman'
            ])
        ;

        dump($qb->getQuery()->getResult()); die;

        dump($project);
        die;

        $maker = null;
        $rootType = 0;

        $structureManager = $this->manager('structure');

        $profileSubtype = 'roman';
        $profiles = [];
        /** @var \AppBundle\Entity\Component\StructureInterface $profile */
        foreach($structureManager->findBy(['type' => 'perfil', 'subtype' => $profileSubtype]) as $profile){
            $profiles[] = new Profile($profile->getCode(), $profile->getDescription(), $profile->getSize());
        }

        //dump($profiles); die;
        /*$dataProfiles = json_decode('[{"id":"1","codigo":"SC004SSRR6MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 6,3MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"6.3"},{"id":"2","codigo":"SSRR4MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 4,2MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"4.2"},{"id":"3","codigo":"SSRR3MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 3,15MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"3.15"},{"id":"4","codigo":"SSRR2MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 2,10MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"2.1"},{"id":"5","codigo":"SSRR1MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 1,57 MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"1.575"}]');

        //dump($dataProfiles);
        $profiles = [];
        foreach ($dataProfiles as $dataProfile){
            $profile = new Profile();
            $profile
                ->setId($dataProfile->id)
                ->setDescription($dataProfile->descricao)
                ->setSize((float) $dataProfile->tamanho)
            ;

            $profiles[] = $profile;
        }

        //dump($profiles); die;*/

        $structure = new Structure();

        /** @var \AppBundle\Entity\Component\ProjectModuleInterface $projectModule */
        $projectModule = $project->getProjectModules()->first();

        $module = new \AppBundle\Util\KitGenerator\StructureCalculator\Module();

        $module
            ->setCellNumber($projectModule->getModule()->getCellNumber())
            ->setLength(1.65)
            ->setWidth(0.992)
            ->setQuantity($projectModule->getQuantity())
        ;

        /** @var \AppBundle\Entity\Component\StructureInterface $createItem */
        /*$createItem = $structureManager->create();
        $createItem
            ->setCode('BASE-GANCHO-SICES')
            ->setType('base')
            ->setSubtype('gancho')
            ->setDescription('SICES SOLAR GANCHO AISI 316 - TELHAS REGULAÇÃO 2 PONTOS - NACIONAL')
            //->setSize(0.035)
        ;
        $structureManager->save($createItem);
        dump($createItem); die;*/


        $terminalF = $structureManager->findOneBy(['type' => 'terminal', 'subtype' => 'final']);
        //dump($terminalF); die;

        // Terminal Final
        $terminalFinal = new Item();
        $terminalFinal
            ->setId($terminalF->getCode())
            ->setDescription($terminalF->getDescription())
            ->setType(Item::TYPE_TERMINAL)
            ->setSubtype(Item::TERMINAL_FINAL)
            ->setSize($terminalF->getSize())
        ;

        //dump($terminalF); die;

        $terminalM = $structureManager->findOneBy(['type' => 'terminal', 'subtype' => 'intermediario']);
        //dump($terminalM); die;

        // Terminal Intermediário
        $terminalMiddle = new Item();
        $terminalMiddle
            ->setId($terminalM->getCode())
            ->setDescription($terminalM->getDescription())
            ->setType(Item::TYPE_TERMINAL)
            ->setSubtype(Item::TERMINAL_MIDDLE)
            ->setSize($terminalM->getSize())
        ;

        //dump($terminalMiddle); die;

        $junctionE = $structureManager->findOneBy(['type' => 'juncao']);

        // Junção
        $junction = new Item();
        $junction
            ->setId($junctionE->getCode())
            ->setDescription($junctionE->getDescription())
            ->setType(Item::TYPE_JUNCTION)
        ;
        //dump($junction); die;

        $baseHookE= $structureManager->findOneBy(['type' => 'base', 'subtype' => 'gancho']);

        // BASES
        $baseHook = new Item();
        $baseHook
            ->setId($baseHookE->getCode())
            ->setDescription($baseHookE->getDescription())
            ->setType(Item::TYPE_BASE)
            ->setSubtype(Item::BASE_HOOK)
        ;

        //dump($baseHook); die;

        $baseScrewStr = new Item();
        $baseScrewStr
            ->setId('SSP12X300')
            ->setDescription('SICES SOLAR PARAFUSO ESTRUTURAL AISI 316M12X300 - NACIONAL ')
            ->setType(Item::TYPE_BASE)
            ->setSubtype(Item::BASE_SCREW_STRUCTURAL)
        ;
        $baseTriangleVertical = new Item();
        $baseTriangleVertical
            ->setId('SSTVLAJE')
            ->setDescription('SICES SOLAR TRIANGULO VERTICAL')
            ->setType(Item::TYPE_BASE)
            ->setSubtype(Item::BASE_TRIANGLE_VERTICAL)
        ;
        $baseScrewDrilling = new Item();
        $baseScrewDrilling
            ->setId('SSPFA')
            ->setDescription('SICES SOLAR PARAFUSO METALICO AUTOPERFURANTE')
            ->setType(Item::TYPE_BASE)
            ->setSubtype(Item::BASE_SCREW_DRILLING)
        ;

        // Fixer Screw
        $fixerScrew = new Item();
        $fixerScrew
            ->setId(14)
            ->setDescription('SICES SOLAR PARAFUSO CABECA MARTELO M10 28/15')
            ->setType(Item::TYPE_FIXER)
            ->setSubtype(Item::FIXER_SCREW)
        ;

        // Fixer Nut
        $fixerNut = new Item();
        $fixerNut
            ->setId(15)
            ->setDescription('SICES SOLAR PORCA M10 INOX A2')
            ->setType(Item::TYPE_FIXER)
            ->setSubtype(Item::FIXER_NUT)
        ;

        // Catch
        $catchBand = new Item();
        $catchBand
            ->setId('21000105')
            ->setDescription('M EPDM BAND 30x3, PU=8 FITA EPDM')
            ->setType(Item::TYPE_CATCH)
            ->setSubtype(Item::CATCH_BAND)
        ;
        $catchSpeedClip = new Item();
        $catchSpeedClip
            ->setId('K21001164')
            ->setDescription('K2 System_SPEEDCLIP')
            ->setType(Item::TYPE_CATCH)
            ->setSubtype(Item::CATCH_SPEED_CLIP)
        ;

        $structure
            ->setMaker(Structure::MAKER_SICES_SOLAR)
            ->setRoofType(Structure::ROOF_ROMAN_AMERICAN)
            ->setModule($module)
            ->addItem($terminalFinal)
            ->addItem($terminalMiddle)
            ->addItem($junction)
            ->addItem($baseHook)              // ROOF_ROMAN_AMERICAN
            //->addItem($baseScrewStr)          // FIBERGLASS
            //->addItem($baseTriangleVertical)  // FLAT_SLAB
            //->addItem($baseScrewDrilling)
            ->addItem($fixerScrew)
            ->addItem($fixerNut)
            //->addItem($catchBand)
            //->addItem($catchSpeedClip)
            ->setProfiles($profiles)
        ;

        $structure->calculate();

        dump($structure->getProfiles());
        dump($structure->getItems());
        die;
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


        dump(json_encode($data)); die;
    }

}
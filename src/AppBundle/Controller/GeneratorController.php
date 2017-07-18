<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\StructureInterface;
use AppBundle\Form\Extra\KitGeneratorType;
use AppBundle\Service\StringBoxCalculator\StringBoxCalculator;
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



        dump($stringBoxes); die;
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


        dump(json_encode($data)); die;
    }

}
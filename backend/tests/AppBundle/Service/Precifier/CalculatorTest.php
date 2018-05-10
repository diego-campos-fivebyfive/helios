<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 5/9/18
 * Time: 2:50 PM
 */

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Service\Precifier\Calculator;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CalculatorTest
 * @group precifier_calculator
 */
class CalculatorTest extends WebTestCase
{
    private $ranges = [
        [
            'id' => 1,
            'memorial_id' => 258,
            'component_id' => 123,
            'family' => 'inverter',
            'code' => 1,
            'cost_price' => 150,
            'metadata' => [
                'partner' => [
                    60 => [
                        'markup' => 0.1,
                        'price' => 100
                    ],
                    70 => [
                        'price' => 200
                    ],
                    80 => [
                        'price' => 300
                    ]
                ]
            ]
        ],
        [
            'id' => 2,
            'memorial_id' => 258,
            'component_id' => 345,
            'family' => 'module',
            'code' => 2,
            'cost_price' => 250,
            'metadata' => [
                'partner' => [
                    60 => [
                        'markup' => 0.2,
                        'price' => 130
                    ],
                    70 => [
                        'price' => 230
                    ],
                    80 => [
                        'price' => 330
                    ]
                ]
            ]
        ]
    ];

    public function testPrecify()
    {
        $data = [
            "level" => 'partner',
            "power" => 78,
            "groups" => [
                'inverter' => [
                    123
                ],
                'module' => [
                    345
                ]
            ]
        ];

        $precifiedComponents = Calculator::precify($data, $this->ranges);

        self::assertEquals(2, count($precifiedComponents));
        self::assertEquals(200, $precifiedComponents['inverter'][123]);
        self::assertEquals(230, $precifiedComponents['module'][345]);
    }
}

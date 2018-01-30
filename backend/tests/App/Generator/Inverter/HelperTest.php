<?php

namespace Tests\App\Generator\Inverter;

use App\Generator\Inverter\Helper;
use Tests\App\Generator\GeneratorTest;

/**
 * Class HelperTest
 * @group generator_inverter_helper
 */
class HelperTest extends GeneratorTest
{
    /**
     * Test power is adjusted
     */
    public function testAdjustPower()
    {
        $inverters = [
            ['nominal_power' => 4],
            ['nominal_power' => 5],
            ['nominal_power' => 6],
            ['nominal_power' => 7],
        ];

        $desiredPower = 0;
        $fdiMax = 1.12;

        $this->assertGreaterThan(0, Helper::adjustPower($inverters, $desiredPower, $fdiMax));
    }

    /**
     * // TODO Sample
     * 1. Criar o método de teste (ex: testMpptOperations)
     * 2. Copiar a função do arquivo de origem e colar na classe de teste (inv_get_mppt_op)
     * 3. Assinar a função copiada como private
     * 4. Gerar os parâmetros de entrada (neste caso um array de inversores contendo ao menos "mppt_number" e "mppt_parallel")
     * 5. Efetuar uma chamada ao método e guardar o resultado em uma variável - Esta pode ser printada para análise.
     * 6. Efetuar um teste com o resultado
     * 7. Criar o método na classe designada (do exemplo: Generator\Inverter\Helper::mpptOperations())
     * 8. Levar o conteúdo da função copiada para o método criado
     * 9. Alterar a chamada no teste para o método criado
     * 10. Refatorar a função na classe (padrão de nomes, etc)
     * 11. Testar novamente.
     * 12. Caso o resultado do teste seja positivo, o processo está concluído
     * 13. Remover a função privada interna (Aqui foi mantido e comentado apenas para exemplo)
     * 14. No arquivo de origem: Marcar a função com TEST OK e colocar seu nome na lista do topo
     */
    public function testMpptOperations()
    {
        $inverters = [
            ['mppt_number' => 2, 'mppt_parallel' => 1],
            ['mppt_number' => 1, 'mppt_parallel' => 1],
            ['mppt_number' => 3, 'mppt_parallel' => 0],
            ['mppt_number' => 1, 'mppt_parallel' => 1],
            ['mppt_number' => 2, 'mppt_parallel' => 0],
        ];

        // 5
        // $mppts = $this->inv_get_mppt_op($inverters);
        // print_r($mppts); die; // TODO: libere este comment para avaliar

        // 9
        $mppts = Helper::mpptOperations($inverters);

        // Testando o inversor com 3 mppts
        $this->assertCount(3, $mppts[2]);
    }

    public function testAllCombination()
    {
        //$combinations = $this->all_combination(3,3);
        $combinations = Helper::allCombinations(3,3);
        //print_r($combinations); die;

        self::assertCount(10, $combinations);
        self::assertCount(3, $combinations[9]);
    }

    public function testAllCombinationOpt()
    {
        //$combinations = $this->all_combination_opt(3,3);
        $combinations = Helper::allCombinationsOptimized(3,3);
        //print_r($combinations); die;

        self::assertCount(10, $combinations);
        self::assertCount(3, $combinations[9]);
    }

    public function testFilterActives()
    {
        $data = [
            [
                'id' => 100,
                'nominal_power' => 1.1,
                'active' => false,
                'alternative' => 1000,
            ],
            [
                'id' => 200,
                'nominal_power' => 1.2,
                'active' => true,
                'alternative' => 400
            ]
        ];

        $alternatives = [
            [
                'id' => 1000,
                'nominal_power' => 1.3,
                'active' => false,
                'alternative' => 3000
            ],
            [
                'id' => 3000,
                'nominal_power' => 1.4,
                'active' => false,
                'alternative' => 4000
            ],
            [
                'id' => 4000,
                'nominal_power' => 1.5,
                'active' => true,
                'alternative' => 3000
            ]
        ];

        $data = Helper::filterActives($data,$alternatives);

//        $data = $this->inv_active_alternative_filter($data,$alternatives);
//        print_r($data);die;

        self::assertCount(2, $data);
        self::assertTrue(4000 == $data[0]["id"] or 4000 == $data[1]["id"]);
        self::assertTrue(200 == $data[1]["id"] or 200 == $data[0]["id"]);
    }

    public function testFilterPhases()
    {
        $data = [
            [
                'id' => 100,
                'phase_number' => 1,
                'phase_voltage' => 220,
                'compatibility' => 0,
            ],
            [
                'id' => 101,
                'phase_number' => 2,
                'phase_voltage' => 220,
                'compatibility' => 0
            ],
            [
                'id' => 102,
                'phase_number' => 3,
                'phase_voltage' => 220,
                'compatibility' => 0
            ],
            [
                'id' => 103,
                'phase_number' => 2,
                'phase_voltage' => 380,
                'compatibility' => 1
            ],
            [
                'id' => 104,
                'phase_number' => 3,
                'phase_voltage' => 380,
                'compatibility' => 1
            ],
            [
                'id' => 105,
                'phase_number' => 3,
                'phase_voltage' => 220,
                'compatibility' => 1
            ]
        ];

        $data = Helper::filterPhases($data,220,2);

//        $data = $this->inv_phase_filter($data,220,2);
//        print_r($data);die;

        self::assertCount(3, $data);
        self::assertEquals(100 , $data[0]["id"]);
        self::assertEquals(101 , $data[1]["id"]);
        self::assertEquals(103 , $data[2]["id"]);
    }

    public function testFilterPower()
    {
        $data = [
            [
                'id' => 100,
                'phase_number' => 1,
                'pow_max_show' => 15,
                'pow_min_show' => 1,
            ],
            [
                'id' => 101,
                'phase_number' => 1,
                'pow_max_show' => 74,
                'pow_min_show' => 15
            ],
            [
                'id' => 102,
                'phase_number' => 3,
                'pow_max_show' => 200,
                'pow_min_show' => 30
            ],
            [
                'id' => 103,
                'phase_number' => 3,
                'pow_max_show' => 600,
                'pow_min_show' => 75
            ],
            [
                'id' => 104,
                'phase_number' => 2,
                'pow_max_show' => 74,
                'pow_min_show' => 15
            ],
            [
                'id' => 105,
                'phase_number' => 2,
                'pow_max_show' => 600,
                'pow_min_show' => 15
            ]
        ];

        $data = Helper::filterPower($data, 50);

//        $data = $this->inv_power_filter($data,50);
//        print_r($data);die;

        self::assertCount(4, $data);
        self::assertEquals(101 , $data[0]["id"]);
        self::assertEquals(102 , $data[1]["id"]);
        self::assertEquals(104 , $data[2]["id"]);
        self::assertEquals(105 , $data[3]["id"]);
    }

    public function testInverterChoices()
    {
        $data = [
            [
                'id' => 100,
                'nominal_power' => 5
            ],
            [
                'id' => 101,
                'nominal_power' => 8
            ],
            [
                'id' => 102,
                'nominal_power' => 12
            ],
            [
                'id' => 103,
                'nominal_power' => 18
            ],
            [
                'id' => 104,
                'nominal_power' => 25
            ],
            [
                'id' => 105,
                'nominal_power' => 32
            ]
        ];

        $data = Helper::inverterChoices($data,70, 1,4);

//       $data = $this->inv_choice($data,70, 1,4);
//        print_r($data);die;

        self::assertCount(3, $data);
        self::assertEquals(104 , $data[0]["id"]);
        self::assertEquals(104 , $data[1]["id"]);
        self::assertEquals(104 , $data[2]["id"]);
    }

    public function testInverterPowerBalace()
    {
        $data = [
            [
                'id' => 100,
                'nominal_power' => 5
            ],
            [
                'id' => 101,
                'nominal_power' => 8
            ],
            [
                'id' => 102,
                'nominal_power' => 12
            ],
            [
                'id' => 103,
                'nominal_power' => 18
            ],
            [
                'id' => 104,
                'nominal_power' => 25
            ],
            [
                'id' => 105,
                'nominal_power' => 32
            ]
        ];

        $data = Helper::powerBalance($data, 70);

        //print_r($data);die;

        self::assertCount(6, $data);
        self::assertEquals(12.6 , $data[3]);
    }

    public function testInverterGetInProtection()
    {
        $data = [
            [
                'id' => 100,
                'nominal_power' => 5,
                'in_protection' => 0
            ],
            [
                'id' => 101,
                'nominal_power' => 8,
                'in_protection' => 1
            ],
            [
                'id' => 102,
                'nominal_power' => 12,
                'in_protection' => 0
            ],
            [
                'id' => 103,
                'nominal_power' => 18,
                'in_protection' => 0
            ],
            [
                'id' => 104,
                'nominal_power' => 12,
                'in_protection' => 1
            ]
        ];

        $data = Helper::hasProtection($data);

        //$data = $this->inv_get_in_protections($data);
        //print_r($data);die;

        self::assertCount(5, $data);
        self::assertEquals(0 , $data[3]);
        self::assertEquals(1 , $data[1]);
    }

    public function testAllarrangements()
    {
        $inverter = [
            'id' => 6431,
            'max_dc_voltage' => 600,
            'mppt_min' => 180,
            'mppt_max_dc_current' => 12.5,
            'mppt_number' => 1
        ];

        $invMpptOperation = [6,3,2,2,1];

        $module = [
            'id' => 46131,
            'max_power' => 270,
            'open_circuit_voltage' => 37.9,
            'voltage_max_power' => 30.8,
            'temp_coefficient_voc' => -0.41,
            'short_circuit_current' => 9.32
        ];

        $data = Helper::allArrangements($inverter, $invMpptOperation, $module);

        //print_r($data);die;

        self::assertNotNull($data);
        self::assertCount(5, $data);
    }

    public function testAutoArrangementChoice()
    {
        $inverter = [
            'id' => 6431,
            'max_dc_voltage' => 600,
            'mppt_min' => 180,
            'mppt_max_dc_current' => 12.5,
            'mppt_number' => 1
        ];

        $invMpptOperation = [6,3,2,2,1];

        $module = [
            'id' => 46131,
            'max_power' => 270,
            'open_circuit_voltage' => 37.9,
            'voltage_max_power' => 30.8,
            'temp_coefficient_voc' => -0.41,
            'short_circuit_current' => 9.32
        ];

        $allArragements = Helper::allArrangements($inverter, $invMpptOperation, $module);

        $data = Helper::autoArrangement($allArragements, 60, $invMpptOperation);

        //print_r($data);die;

        self::assertCount(5, $data);
        self::assertCount(3, $data[0]);
        self::assertArrayHasKey('par', $data[0]);

    }
}

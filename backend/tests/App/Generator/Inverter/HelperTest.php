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

    /*
    private function inv_get_mppt_op ($all_inv){

        //$inv = $all_inv;

        $mppt_op = array();
        for ($i=0; $i<count($all_inv); $i++){
            $mppt_parallel = $all_inv[$i]["mppt_parallel"];
            $mppt_number = $all_inv[$i]["mppt_number"];

            if ($mppt_parallel == 1){
                $mppt_op[$i][0] = $mppt_number;
            }else{
                for ($m=0; $m<$mppt_number; $m++){
                    $mppt_op[$i][$m] = 1;
                }
            }

        }
        return $mppt_op;
    }
    */
}

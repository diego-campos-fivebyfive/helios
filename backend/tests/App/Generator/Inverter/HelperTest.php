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

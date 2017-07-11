<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Form\Extra\KitGeneratorType;
use AppBundle\Service\KitGenerator\InverterLoader;
use AppBundle\Util\KitGenerator\InverterCombiner\Module;
use AppBundle\Util\KitGenerator\PowerEstimator\PowerEstimator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("generator")
 */
class GeneratorController extends AbstractController
{
    /**
     * @Route("/kit", name="generate_kit")
     */
    public function indexAction(Request $request)
    {
        ini_set('max_execution_time', '120');

        $data = [
            'latitude' => -15.79,
            'longitude' => -47.88
        ];

        $modId = 32433;
        /** @var ModuleInterface $module */
        $module = $this->manager('module')->find($modId);
        $pot = $this->previewPower(10000, $data['latitude'], $data['longitude']);

        // Makers
        $makers = $this->manager('maker')->findBy([
            'context' => 'component_inverter'
        ], null, 1);

        $inverters = [];
        foreach ($makers as $key => $maker) {
            dump($key);
            $inverters[] = $this->calculateInverter($pot, $module, $maker);
        }

        dump($inverters); die;

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

    private function sign($number)
    {
        if ($number < 0) {
            return -1;
        } else {
            return 1;
        }
    }

    private function prev_pot($kwh, $lat, $lon)
    {
        // SYSTEM INPUTS
        return $this->previewPower($kwh, $lat, $lon);
    }

    private function fatorial($num)
    {
        $acu = $num;
        if ($num == 0) {
            return 1;
        } else {
            for ($i = $num - 1; $i != 0; $i--) {
                $acu *= $i;
            }
            return $acu;
        }
    }

    private function comb_rep($a, $b)
    {
        $numerador = $a + $b - 1;
        $cont = $b;
        for ($i = $numerador - 1; $cont > 1; $i--) {
            $numerador *= $i;
            $cont -= 1;
        }
        return $numerador / $this->fatorial($b);
    }

    private function calc_inv($kw, $mod_id, $inv_maker)
    {
        $busca_mod_bd = $mod_id;
        $pot_mod = $kw;
        $fdi_max = 1;
        $fdi_min = 0.75;
        $lim_sup = $pot_mod * $fdi_max;
        $lim_inf = $pot_mod * $fdi_min;

        $inv_index = array();
        $comb = 1;

        $busca_inv_bd = $this->findInverter($lim_inf, $lim_sup, $comb);

        if ($comb == 2 and count($busca_inv_bd) >= 1) {

            for ($j = $comb; $j <= 50; $j++) {

                $cont = array($j);
                for ($i = 0; $i < $j; $i++) {
                    $cont[$i] = 0;
                }

                $max = count($busca_inv_bd) - 1;
                for ($i = 0; $i < $this->comb_rep(count($busca_inv_bd), $j); $i++) {

                    $result = 0;
                    for ($y = 0; $y < count($cont); $y++) {
                        $result += $busca_inv_bd[$cont[$y]]["nominalPower"];
                    }

                    if ($result <= $lim_sup and $result >= $lim_inf) {
                        break 2;
                    }

                    $cont[$j - 1] += 1;
                    for ($k = 1; $k < $j; $k++) {
                        if ($cont[$j - $k] > $max) {
                            $cont[$j - ($k + 1)] += 1;
                            for ($z = $k; $z >= 1; $z--) {
                                $cont[$j - $z] = $cont[$j - ($z + 1)];
                            }
                        }
                    }
                }
            }

            if (count($cont) == 50) {
                return false;
                die();
            }

            rsort($cont);

            $inv_index[0]["inv"] = $cont[0];
            $inv_index[0]["qte"] = 1;
            $contador = 1;
            for ($i = 1; $i < count($cont); $i++) {
                if ($cont[$i] == $cont[$i - 1]) {
                    $inv_index[$contador - 1]["qte"] += 1;
                } else {
                    $inv_index[$contador]["inv"] = $cont[$i];
                    $inv_index[$contador]["qte"] = 1;
                    $contador += 1;
                }
            }
        }

        if (count($inv_index) == 0) {
            //$inv_out = null;
            return false;
            die();
        }

//-------- INICIANDO CÁLCULO DE MÓDULOS COM STRIGS DOS INVERSORES --------
        $pot_total_inv = 0;
        for ($i = 0; $i < count($inv_index); $i++) {
//        $model = $busca_inv_bd[$inv_index[$i]["inv"]]["model"];
//        $qte = $inv_index[$i]["qte"];
            $pot_total_inv += $busca_inv_bd[$inv_index[$i]["inv"]]["nominalPower"] * $inv_index[$i]["qte"];
            //echo "$qte - $model<br>";
        }
//calculando o percentual de potêcia para cada inversor baseada na potência total desejada
        $percent_inv = array();
        for ($i = 0; $i < count($inv_index); $i++) {
            $percent_inv[$i] = $busca_inv_bd[$inv_index[$i]["inv"]]["nominalPower"] / $pot_total_inv;
            //echo "$percent_inv[$i]<br>";
        }
//echo "$pot_total_inv<br>";

        $tnoct = 45;
        $tc_min = 10;
        $tc_max = 70;

        //$vmax_mod = $busca_mod_bd[0]["open_circuit_voltage"];
        $vmax_mod = $busca_mod_bd->getOpenCircuitVoltage();
        //$vmin_mod = $busca_mod_bd[0]["voltage_max_power"] * (1 + (($tc_max - $tnoct) * ($busca_mod_bd[0]["temp_coefficient_voc"] / 100)));
        $vmin_mod = $busca_mod_bd->getVoltageMaxPower() * (1 + (($tc_max - $tnoct) * ($busca_mod_bd->getTempCoefficientVoc() / 100)));

        //var_dump($busca_mod_bd); die;

// calculando número de strings e módulos/string de cada inversor
        for ($i = 0; $i < count($inv_index); $i++) {
            $qte_max_mod_ser = floor($busca_inv_bd[$inv_index[$i]["inv"]]["maxDcVoltage"] / $vmax_mod);
            $qte_min_mod_ser = ceil($busca_inv_bd[$inv_index[$i]["inv"]]["mpptMin"] / $vmin_mod);
            $qte_max_mod_par = floor(($busca_inv_bd[$inv_index[$i]["inv"]]["mpptMaxDcCurrent"] * $busca_inv_bd[$inv_index[$i]["inv"]]["mpptNumber"]) / ($busca_mod_bd->getShortCircuitCurrent()));

            //echo "<br>Max série = $qte_max_mod_ser<br>Min série = $qte_min_mod_ser<br>Max par = $qte_max_mod_par<br><br>";

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($busca_mod_bd->getMaxPower() / 1000);
                    $n_mod = $p * $s;
                    //echo "$p x $s = $n_mod = $pot kWp<br>";
                    if ($pot >= ($pot_mod * $percent_inv[$i])) {
                        $inv_index[$i]["ser"] = $s;
                        $inv_index[$i]["par"] = $p;
                        //echo "$p x $s = $n_mod = $pot kWp<br>";
                        break 2;
                    }
                }
            }
        }
        $inv_out = array();
        for ($i = 0; $i < count($inv_index); $i++) {
            $inv_out[$i]["inv_id"] = $busca_inv_bd[$inv_index[$i]["inv"]]["id"];
            $inv_out[$i]["descricao"] = $busca_inv_bd[$inv_index[$i]["inv"]]["model"];
            $inv_out[$i]["qte"] = $inv_index[$i]["qte"];
            $inv_out[$i]["ser"] = $inv_index[$i]["ser"];
            $inv_out[$i]["par"] = $inv_index[$i]["par"];
        }
        //print_r($inv_out); die;
        return $inv_out;
    }

    private function calc_est($maker, $qte_mod, $tipo_telhado, $posicao, $module)
    {
        $total_mod = $qte_mod;
        $tipo_telhado = $tipo_telhado;
// 0 = Telhas Romanas e Americanas
// 1 = Telhas de Fibrocimento
// 2 = Laje Plana
// 3 = Chapa Metalica
// 4 = Chapa Metalica (Perfil de 0,5m)

        $posicao = $posicao;
// 0 = Vertical
// 1 = Horizontal

        $qte_linhas_mod = 1; //sempre será 1

        $tam_max_perfil = 0;
//definição de regras para tamanho máximo de perfil
        if ($total_mod <= 52) {
            $tam_max_perfil = 1;
            if ($total_mod <= 26) {
                $tam_max_perfil = 2;
                if ($total_mod <= 12) {
                    $tam_max_perfil = 3;
                }
            }
        }

        //var_dump($tam_max_perfil); die;

        //definição dos dados do módulo
        //$mod_bd = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id ORDER BY max_power ASC");
        $mod_bd = $module;
        $n_cell = $mod_bd->getCellNumber();
        //$mod_comprimento = $mod_bd->getLength();//
        //$mod_largura = $mod_bd->getWidth();

        $mod_comprimento = 1.65;
        $mod_largura = 0.992;

        //var_dump($posicao); die;

        //setar limetes de módulos por linha na horizontal ou vertical
        //para módulos na vertical
        $dim_usada = $mod_largura;
        $mod_lim = 12; //Distribution
        if ($total_mod > 52) {
            $mod_lim = 18;
        }

        //para módulos na horizontal
        if ($posicao == 1) {
            $dim_usada = $mod_comprimento;
            //para 72 células
            $mod_lim = 6;
            //para 60 células
            if ($n_cell == 60) {
                $mod_lim = 7;
                if ($total_mod > 52) {
                    $mod_lim = 11;
                }
            }

        }

        //var_dump($mod_lim); die;

        //var_dump($mod_lim); die;
        //quebrando a quantidade de módulos em grupos
        $grupos = array(ceil($total_mod / $mod_lim));
        //var_dump($grupos); die;
        $resto_mod = $total_mod;
        //var_dump($resto_mod); die;
        for ($i = 0; $i < ceil($total_mod / $mod_lim); $i++) {
            $grupos[$i] = $resto_mod;
            if ($resto_mod > $mod_lim) {
                $grupos[$i] = $mod_lim;
                $resto_mod -= $mod_lim;
            }
        }

        //var_dump($grupos); die;

        //------- ACESSANDO BD --------
        $subtipo = "roman";
        if ($tipo_telhado == 3 or $tipo_telhado == 4) {
            $subtipo = "industrial";
        }

        //dump($subtipo); die;

        //$data2 = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'perfil' AND subtipo = '$subtipo' ORDER BY tamanho DESC");

        $data2 = json_decode('[{"id":"1","codigo":"SC004SSRR6MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 6,3MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"6.3"},{"id":"2","codigo":"SSRR4MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 4,2MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"4.2"},{"id":"3","codigo":"SSRR3MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 3,15MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"3.15"},{"id":"4","codigo":"SSRR2MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 2,10MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"2.1"},{"id":"5","codigo":"SSRR1MT","descricao":"SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 1,57 MT","maker":"1","tipo":"perfil","subtipo":"roman","tamanho":"1.575"}]', true);

        //dump($data2); die;

        //$term_final_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'terminal' AND subtipo = 'final'");
        //$term_inter_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'terminal' AND subtipo = 'intermediario'");
        $term_final_bd = json_decode('[{"id":"12","codigo":"SSTF","descricao":"SICES SOLAR TERMINAL FINAL 39..41MM for CAN - NACIONAL","maker":"1","tipo":"terminal","subtipo":"final","tamanho":"0.035"}]', true);
        $term_inter_bd = json_decode('[{"id":"13","codigo":"SSTI","descricao":"SICES SOLAR TERMINAL INTERMEDIARIO 39..44MM for CAN\/AVP - NACIONAL","maker":"1","tipo":"terminal","subtipo":"intermediario","tamanho":"0.012"}]', true);
        //var_dump($term_inter_bd[0]); die;
        //var_dump($tam_max_perfil); die;

        $ter_final_largura = (float)$term_final_bd[0]["tamanho"];
        $ter_int_largura = (float)$term_inter_bd[0]["tamanho"];

        /*foreach($data2 as $item){
             var_dump($item['tamanho']);
         }
         die;*/

        //var_dump($data2); die;
        /*$perfis = array_map(function($data){
            return $data["tamanho"];
        }, $data2);*/

        //var_dump($ter_final_largura); die;
        //var_dump($ter_final_largura . ' - ' . $ter_int_largura); die;


        //inicializando quantidades de perfis
        $total_perfil_usado = array(count($data2));
        for ($i = 0; $i < count($data2); $i++) {
            $total_perfil_usado[$i] = 0;
            //print_r($data2[$i]);
        }

        //die;

        //var_dump($total_perfil_usado); die;

        $total_juncao = 0;
        $total_term_final = 0;
        $total_term_inter = 0;
        $total_perfil_chapa_meio = 0;
        $total_base = 0;
        $total_parafuso_martelo = 0;
        $total_porca_m10 = 0;
        $total_parafuso_est = 0;
        $total_parafuso_auto = 0;
        $total_speedclip = 0;
        $total_triangulo = 0;
        $total_fita = 0;

        //var_dump($data2[0]); die;

        //------- INÍCIO DO CÁLCULO DAS ESTRUTURAS --------

        //echo "<table border=1>";
        //echo "<tr><td> Nº de módulos </td><td> 6,30 </td><td> 4,20 </td><td> 3,15 </td><td> 2,10 </td><td> 1,57 </td><td> Junções </td><td>Terminal final</td><td>Terminal inter</td></tr>";

        for ($lac = 0; $lac < count($grupos); $lac++) {

            $qte_mod = $grupos[$lac];

            $tam_linha = ($qte_mod * $dim_usada) + (($qte_mod - 1) * $ter_int_largura) + (2 * $ter_final_largura);

            //var_dump($tam_linha); die;

            $perfil_usado = array(count($data2));

            //inicializando quantidades de perfis
            for ($i = 0; $i < count($data2); $i++) {
                $perfil_usado[$i] = 0;
            }

            ///print_r($perfil_usado); die;

            $perfil_usado[$tam_max_perfil] = floor($tam_linha / $data2[$tam_max_perfil]["tamanho"]);

            //var_dump($perfil_usado); die;
            //print_r($data2[$tam_max_perfil]); die;

            $resto_opc1 = (($tam_linha / $data2[$tam_max_perfil]["tamanho"]) - $perfil_usado[$tam_max_perfil]) * $data2[$tam_max_perfil]["tamanho"];

            //var_dump($resto_opc1); die;

            // definição do primeiro maior perfil em relação à resto_opc1
            $primeiro_maior_opc1 = 0;
            for ($i = count($data2) - 1; $i >= $tam_max_perfil; $i--) {
                $primeiro_maior_opc1 = $i;
                if (($resto_opc1 - $data2[$i]["tamanho"]) < 0) {
                    break;
                }
            }

            //var_dump($primeiro_maior_opc1); die;

            if (($resto_opc1 * 2) > $data2[$primeiro_maior_opc1]["tamanho"]) {
                if ($tam_max_perfil == $primeiro_maior_opc1) {
                    $perfil_usado[$tam_max_perfil] = ($perfil_usado[$tam_max_perfil] * 2) + 2;
                } else {
                    $perfil_usado[$tam_max_perfil] = ($perfil_usado[$tam_max_perfil] * 2);
                    $perfil_usado[$primeiro_maior_opc1] += 2;
                }
            } else {
                $perfil_usado[$tam_max_perfil] *= 2;
                if ($tam_max_perfil == $primeiro_maior_opc1) {
                    $perfil_usado[$tam_max_perfil] += 1;
                } else {
                    $perfil_usado[$primeiro_maior_opc1] += 1;
                }
            }

            //var_dump($resto_opc1); die;

            //calculando sobra
            $tamanho_usado = 0;
            for ($i = 0; $i < count($data2); $i++) {
                $tamanho_usado += $perfil_usado[$i] * $data2[$i]["tamanho"];
            }

            //var_dump($tamanho_usado); die;

            $sobra = $tamanho_usado - ($tam_linha * 2);

            //var_dump($sobra); die;

            //redistribuir caso a sobra tenha ficado muito grande (> que 2m)
            if ($sobra > 2) {
                $perfil_usado[$tam_max_perfil] -= 1;

                //recalculando sobra
                $tamanho_usado = 0;
                for ($i = 0; $i < count($data2); $i++) {
                    $tamanho_usado += $perfil_usado[$i] * $data2[$i]["tamanho"];
                }
                $sobra = abs($tamanho_usado - ($tam_linha * 2));

                $a = 0;
                for ($i = count($data2) - 1; $i >= $tam_max_perfil; $i--) {
                    $a = $i;
                    if (($sobra - $data2[$i]["tamanho"]) < 0) {
                        break;
                    }
                }

                $valor = $data2[$a]["tamanho"];
                $valor_dividido = $valor / 2;
                $key = false;
                for ($i = 0; $i < count($data2); $i++) {
                    if ($data2[$i]["tamanho"] == $valor_dividido) {
                        $key = $i;
                    }
                }
                if ($key == false) {
                    $perfil_usado[$a] += 1;
                } else {
                    $perfil_usado[$key] += 2;
                }
            }

            //calculando número de junções
            $juncao = array_sum($perfil_usado);

            //var_dump($juncao); die;

            if (($juncao % 2) != 0) {
                $juncao += 1;
            }
            $juncao -= 2;

            //var_dump($juncao); die;

            //multiplicando pelo número de linhas de módulos
            for ($i = 0; $i < count($perfil_usado); $i++) {
                $perfil_usado[$i] *= $qte_linhas_mod;
            }

            //var_dump($perfil_usado); die;

            $juncao *= $qte_linhas_mod;
            $term_final = 4 * $qte_linhas_mod;
            $term_inter = ($qte_mod - 1) * 2 * $qte_linhas_mod;
            $perfil_chapa_meio = ($term_final + $term_inter);
            $base = 2 * (ceil(($tam_linha - (2 * 0.35)) / 1.65) + 1) * $qte_linhas_mod;

            //var_dump($base); die;

            if ($qte_mod == 1) {
                $base = 4 * $qte_linhas_mod;
            }

            //var_dump($base); die;

            $parafuso_martelo = $base;
            $porca_m10 = $base;
            $parafuso_est = $base;
            //$parafuso_auto = ceil(4 * (($tam_linha / 0.4) + 1)) * $qte_linhas_mod;
            $parafuso_auto = 4 * (ceil(($tam_linha) / 0.4) + 1) * $qte_linhas_mod;
            //var_dump($parafuso_auto); die;
            if ($tipo_telhado == 4) {
                $parafuso_auto = $perfil_chapa_meio * 4;
            }

            //var_dump($); die;

            $speedclip = $parafuso_auto / 2;
            //$parafuso_auto_meio = $perfil_chapa_meio * 4;
            $triangulo = (ceil(($tam_linha - (2 * 0.35)) / 1.65) + 1) * $qte_linhas_mod;
            //$fita = 2 * (ceil(($tam_linha) / 0.4) + 1) * 0.065 * $qte_linhas_mod;
            $fita = ($parafuso_auto / 2) * 0.1;

            //var_dump($fita); die;

            //TOTAIS
            $total_juncao += $juncao;
            for ($i = 0; $i < count($data2); $i++) {
                $total_perfil_usado[$i] += $perfil_usado[$i];
            }

            //var_dump($total_juncao); die;/

            $total_term_final += $term_final;
            $total_term_inter += $term_inter;
            $total_perfil_chapa_meio += $perfil_chapa_meio;
            $total_base += $base;
            $total_parafuso_martelo += $parafuso_martelo;
            $total_porca_m10 += $porca_m10;
            $total_parafuso_est += $parafuso_est;
            $total_parafuso_auto += $parafuso_auto;
            $total_speedclip += $speedclip;
            $total_triangulo += $triangulo;
            $total_fita += $fita;

            /*var_dump($total_term_final);
            var_dump($total_term_inter);
            var_dump($total_perfil_chapa_meio);
            var_dump($total_base);
            var_dump($total_parafuso_martelo);
            var_dump($total_porca_m10);
            var_dump($total_parafuso_est);
            var_dump($total_parafuso_auto);
            var_dump($total_speedclip);
            var_dump($total_triangulo);
            var_dump($total_fita);
            die;*/
            //echo "<tr><td>$grupos[$lac]</td><td>$perfil_usado[0]</td><td>$perfil_usado[1]</td><td>$perfil_usado[2]</td><td>$perfil_usado[3]</td><td>$perfil_usado[4]</td><td>$juncao</td><td>$term_final</td><td>$term_inter</td></tr>";
        }

        //var_dump($total_juncao); die;


//echo "</table>";
//
//echo "<table border=1>";
//echo "<tr><td> Nº de módulos </td><td> 6,30 </td><td> 4,20 </td><td> 3,15 </td><td> 2,10 </td><td> 1,57 </td><td> Junções </td><td> Terminal final</td><td> Terminal inter</td></tr>";
//echo "<tr><td>$total_mod</td><td>$total_perfil_usado[0]</td><td>$total_perfil_usado[1]</td><td>$total_perfil_usado[2]</td><td>$total_perfil_usado[3]</td><td>$total_perfil_usado[4]</td><td>$total_juncao</td><td>$total_term_final</td><td>$total_term_inter</td></tr>";
//echo "</table>";
//------- TÉRMINO DO CÁLCULO DAS ESTRUTURAS --------


//------- ITENS PARA QUALQUER OPÇÃO DE TELHADO (Para opção 4 os perfis mudam) --------
//echo "<div class='section'>";
//echo "<div class='container'>";
//echo "<div class='row'>";
//echo "<div class='col-md-12'>";
//
//echo "<table class='table'>";
//echo "<thead><tr><th>Código</th><th>Descrição</th><th>Quantidade</th></tr></thead>";
//echo "<tbody>";

        $est_out = array();
        $aponta = 0;
        if ($tipo_telhado != 4) {
            for ($i = 0; $i < count($data2); $i++) {
                if ($total_perfil_usado[$i] > 0) {
                    $est_out[$aponta]["est_id"] = $data2[$i]["id"];
                    $est_out[$aponta]["descricao"] = $data2[$i]["descricao"];
                    $est_out[$aponta]["qte"] = $total_perfil_usado[$i];
                    $aponta += 1;
                    //var_dump($est_out); die;
//            $cod = $data2[$i]["codigo"];
//            $desc = $data2[$i]["descricao"];
//            $qte = $total_perfil_usado[$i];
//            echo "<tr><td>$cod</td><td>$desc</td><td>$qte</td></tr>";
                }
            }
        } else {
            $perfil_meio_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'perfil' AND subtipo = 'meio_metro'");
            $est_out[$aponta]["est_id"] = $perfil_meio_bd[0]["id"];
            $est_out[$aponta]["descricao"] = $perfil_meio_bd[0]["descricao"];
            $est_out[$aponta]["qte"] = $total_perfil_chapa_meio;
            $aponta += 1;
//    $cod = $perfil_meio_bd[0]["codigo"];
//    $desc = $perfil_meio_bd[0]["descricao"];
//    $qte = $total_perfil_chapa_meio;
//    echo "<tr><td>$cod</td><td>$desc</td><td>$qte</td></tr>";
        }

        //die;

//------- ITENS PARA OPÇÃO DE TELHADO 0, 1 e 2--------
        if ($tipo_telhado == 0 or $tipo_telhado == 1 or $tipo_telhado == 2) {
            if ($total_juncao > 0) {
                //$juncao_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'juncao'");

                $juncao_bd = json_decode('[{"id":"11","codigo":"SSJ8RR","descricao":"SICES SOLAR JUN\u00c7\u00c3O PARA PERFIL EM ALUMINIO\u00a0- NACIONAL","maker":"1","tipo":"juncao","subtipo":"","tamanho":"0"}]', true);

                $est_out[$aponta]["est_id"] = $juncao_bd[0]["id"];
                $est_out[$aponta]["descricao"] = $juncao_bd[0]["descricao"];
                $est_out[$aponta]["qte"] = $total_juncao;
                $aponta += 1;

                //var_dump($juncao_bd[0]); die;
//        $cod_juncao = $juncao_bd[0]["codigo"];
//        $desc_juncao = $juncao_bd[0]["descricao"];
//        echo "<tr><td>$cod_juncao</td><td>$desc_juncao</td><td>$total_juncao</td></tr>";
            }

            //$parafuso_martelo_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'fixador' AND subtipo = 'parafuso'");
            $parafuso_martelo_bd = json_decode('[{"id":"14","codigo":"SSPCM28","descricao":"SICES SOLAR PARAFUSO CABECA MARTELO M10 28\/15","maker":"1","tipo":"fixador","subtipo":"parafuso","tamanho":"0"}]', true);
            //$porca_m10_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'fixador' AND subtipo = 'porca'");
            $porca_m10_bd = json_decode('[{"id":"15","codigo":"SSPM10","descricao":"SICES SOLAR PORCA M10 INOX A2","maker":"1","tipo":"fixador","subtipo":"porca","tamanho":"0"}]', true);

            $est_out[$aponta]["est_id"] = $parafuso_martelo_bd[0]["id"];
            $est_out[$aponta]["descricao"] = $parafuso_martelo_bd[0]["descricao"];
            $est_out[$aponta]["qte"] = $total_parafuso_martelo;
            $aponta += 1;
            $est_out[$aponta]["est_id"] = $porca_m10_bd[0]["id"];
            $est_out[$aponta]["descricao"] = $porca_m10_bd[0]["descricao"];
            $est_out[$aponta]["qte"] = $total_porca_m10;
            $aponta += 1;

            //var_dump($porca_m10_bd[0]); die;
//    $cod_parafuso = $parafuso_martelo_bd[0]["codigo"];
//    $desc_parafuso = $parafuso_martelo_bd[0]["descricao"];
//    $cod_porca_m10 = $porca_m10_bd[0]["codigo"];
//    $desc_porca_m10 = $porca_m10_bd[0]["descricao"];
//    echo "<tr><td>$cod_parafuso</td><td>$desc_parafuso</td><td>$total_parafuso_martelo</td></tr>";
//    echo "<tr><td>$cod_porca_m10</td><td>$desc_porca_m10</td><td>$total_porca_m10</td></tr>";
        }

        //print_r($est_out); die;
        //var_dump($term_final_bd[0]); die;

        $est_out[$aponta]["est_id"] = $term_final_bd[0]["id"];
        $est_out[$aponta]["descricao"] = $term_final_bd[0]["descricao"];
        $est_out[$aponta]["qte"] = $total_term_final;
        $aponta += 1;
//$cod_ter_final = $term_final_bd[0]["codigo"];
//$desc_ter_final = $term_final_bd[0]["descricao"];
//echo "<tr><td>$cod_ter_final</td><td>$desc_ter_final</td><td>$total_term_final</td></tr>";
        if ($total_term_inter > 0) {
            $est_out[$aponta]["est_id"] = $term_inter_bd[0]["id"];
            $est_out[$aponta]["descricao"] = $term_inter_bd[0]["descricao"];
            $est_out[$aponta]["qte"] = $total_term_inter;
            $aponta += 1;
//    $cod_ter_inter = $term_inter_bd[0]["codigo"];
//    $desc_ter_inter = $term_inter_bd[0]["descricao"];
//    echo "<tr><td>$cod_ter_inter</td><td>$desc_ter_inter</td><td>$total_term_inter</td></tr>";
        }

//------- BASES--------
        if ($tipo_telhado == 0) {
            $subtipo_base = "gancho";
            $tam = 0;
            $qte_base = $total_base;
        } elseif ($tipo_telhado == 1) {
            $subtipo_base = "parafuso_estrutural";
            $tam = 0.3;
            $qte_base = $total_base;
        } elseif ($tipo_telhado == 2) {
            $subtipo_base = "triangulo_vertical";
            $tam = 0;
            $qte_base = $total_triangulo;
            if ($posicao == 1) {
                $subtipo_base = "triangulo_horizontal";
            }
        } elseif ($tipo_telhado == 3 or $tipo_telhado == 4) {
            $subtipo_base = "parafuso_autoperfurante";
            $tam = 0;
            $qte_base = $total_parafuso_auto;
            $subtipo_fita_speedclip = "fita";
            if ($maker == 2) {
                $subtipo_fita_speedclip = "speedclip";
            }
            $fita_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'base' AND subtipo = '$subtipo_fita_speedclip'");
            $est_out[$aponta]["est_id"] = $fita_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_fita;
            if ($maker == 2) {
                $est_out[$aponta]["qte"] = $total_speedclip;
            }
            $aponta += 1;
            //var_dump($fita_bd[0]); die;
//    $cod_fita = $fita_bd[0]["codigo"];
//    $desc_fita = $fita_bd[0]["descricao"];
//    echo "<tr><td>$cod_fita</td><td>$desc_fita</td><td>$total_fita</td></tr>";
        }
        //var_dump($subtipo_base); die;
        //$base_bd = R::getAll("SELECT * FROM est_fix WHERE maker = $maker AND tipo = 'base' AND subtipo = '$subtipo_base' AND tamanho = $tam");
        $base_bd = json_decode('[{"id":"16","codigo":"GCHO2P","descricao":"SICES SOLAR GANCHO AISI 316 - TELHAS REGULA\u00c7\u00c3O 2 PONTOS - NACIONAL","maker":"1","tipo":"base","subtipo":"gancho","tamanho":"0"}]', true);
        $est_out[$aponta]["est_id"] = $base_bd[0]["id"];
        $est_out[$aponta]["descricao"] = $base_bd[0]["descricao"];
        $est_out[$aponta]["qte"] = $qte_base;
        $aponta += 1;

        //var_dump($base_bd[0]); die;
        //var_dump($tipo_telhado); die;

        //echo $tam;

        //$cod_base = $base_bd[0]["codigo"];
        //$desc_base = $base_bd[0]["descricao"];
        //echo "<tr><td>$cod_base</td><td>$desc_base</td><td>$qte_base</td></tr>";
        //echo "</tbody>";
        //echo "</table>";

        //echo "<br>";

        if ((count($grupos) == 1) or ($mod_lim == $grupos[count($grupos) - 1])) {
            $n_grupos = count($grupos);
            $ultima_linha = $grupos[count($grupos) - 1];
            $est_out["obs"] = "Obs: estrutura calculada para $n_grupos linha(s) com $ultima_linha módulos";
//    echo "Obs: estrutura calculada para $n_grupos linha(s) com $ultima_linha módulos";
        } else {
            $n_grupos = count($grupos) - 1;
            $ultima_linha = $grupos[$n_grupos];
            $est_out["obs"] = "Obs: estrutura calculada para $n_grupos linha(s) com $mod_lim módulos e uma linha com $ultima_linha módulo(s)";
//    echo "Obs: estrutura calculada para $n_grupos linha(s) com $mod_lim módulos e uma linha com $ultima_linha módulo(s)";
        }

        //var_dump($est_out); die;
        return ($est_out);

    }

    /**
     * @param $lim_inf
     * @param $lim_sup
     * @return array
     */
    private function findInverter($lim_inf, $lim_sup, &$comb = 1)
    {
        $invQueryBuilder = $this->getInverterManager()->getEntityManager()->createQueryBuilder();

        $busca_inv_bd = $invQueryBuilder->select('i')
            ->from(Inverter::class, 'i')
            ->where(
                $invQueryBuilder->expr()->between('i.nominalPower', $lim_inf, $lim_sup)
            )
            ->orderBy('i.nominalPower', 'asc')
            ->getQuery()
            ->getResult(2);

        //$busca_inv_bd = $searchInverter($lim_inf, $lim_sup);
        //dump($busca_inv_bd); die;

        /*if (count($busca_inv_bd) > 0) {
            $inv_index[0]["inv"] = 0;
            $inv_index[0]["qte"] = 1;
        }*/

        if (count($busca_inv_bd) == 0) {
            $comb = 2;
            for ($l = 0.3; $l > 0.025; $l -= 0.015) {
                $busca_inv_bd = $this->findInverter($lim_inf * $l, $lim_sup);
                if (count($busca_inv_bd) >= 2) {
                    break;
                }
            }
        }

        return $busca_inv_bd;
    }

    private function calculateInverter($modPower, ModuleInterface $module, MakerInterface $maker)
    {
        $fdiMin = 0.75;
        $fdiMax = 1;

        $nominalPowerMin = $fdiMin * $modPower;
        $nominalPowerMax = $fdiMax * $modPower;

        /** @var \AppBundle\Manager\InverterManager $manager */
        $manager = $this->manager('inverter');
        $makerManager = $this->manager('maker');

        $inverterLoader = new InverterLoader($manager, $makerManager);
        $inverters = $inverterLoader->loadFromRanges($nominalPowerMin, $nominalPowerMax, $maker);

        $combination = $inverterLoader->getCombination();
        $countInverters = count($inverters);

        if($countInverters){

            $inverters[0]['quantity'] = 2 == $combination ? 0 : 1 ;

            if(2 == $combination) {

                for ($j = $combination; $j <= 50; $j++) {

                    $cont = array_fill(0, $j, 0);

                    $max = $countInverters - 1;
                    $repetitions = $this->comb_rep($countInverters, $j);

                    for ($i = 0; $i < $repetitions; $i++) {

                        $result = 0;
                        for ($y = 0; $y < count($cont); $y++) {
                            if(array_key_exists($cont[$y], $inverters)) {
                                $result += $inverters[$cont[$y]]["nominalPower"];
                            }
                        }

                        if ($result <= $nominalPowerMax && $result >= $nominalPowerMin) {
                            break 2;
                        }

                        $cont[$j - 1] += 1;
                        for ($k = 1; $k < $j; $k++) {
                            if ($cont[$j - $k] > $max) {
                                $cont[$j - ($k + 1)] += 1;
                                for ($z = $k; $z >= 1; $z--) {
                                    $cont[$j - $z] = $cont[$j - ($z + 1)];
                                }
                            }
                        }
                    }
                }

                rsort($cont);

                //$inv_index[0]["inv"] = $cont[0];
                $inverters[0]["quantity"] = 1;
                $contador = 1;
                for ($x = 1; $x < count($cont); $x++) {
                    if ($cont[$x] == $cont[$x - 1]) {
                        $inverters[$contador - 1]["quantity"] += 1;
                    } else {
                        $inverters[$contador]["quantity"] = 1;
                        $contador += 1;
                    }
                }
            }
        }

        foreach ($inverters as $key => $inverter){
            if(!$inverter['quantity'])
                unset($inverters[$key]);
        }

        $totalInverterPower = 0;
        for ($i = 0; $i < count($inverters); $i++) {
            $totalInverterPower += $inverters[$i]['nominalPower'] * 1;
        }

        $percentPower = array();
        for ($i = 0; $i < count($inverters); $i++) {
            $percentPower[$i] = $inverters[$i]["nominalPower"] / $totalInverterPower;
        }

        $tnoct  = 45;
        $tc_min = 10;
        $tc_max = 70;

        //$vmax_mod = $busca_mod_bd[0]["open_circuit_voltage"];
        $vmax_mod = $module->getOpenCircuitVoltage();
        //$vmin_mod = $busca_mod_bd[0]["voltage_max_power"] * (1 + (($tc_max - $tnoct) * ($busca_mod_bd[0]["temp_coefficient_voc"] / 100)));
        $vmin_mod = $module->getVoltageMaxPower() * (1 + (($tc_max - $tnoct) * ($module->getTempCoefficientVoc() / 100)));

        // calculando número de strings e módulos/string de cada inversor
        for ($i = 0; $i < count($inverters); $i++) {
            $qte_max_mod_ser = floor($inverters[$i]["maxDcVoltage"] / $vmax_mod);
            $qte_min_mod_ser = ceil($inverters[$i]["mpptMin"] / $vmin_mod);
            $qte_max_mod_par = floor(($inverters[$i]["mpptMaxDcCurrent"] * $inverters[$i]["mpptNumber"]) / ($module->getShortCircuitCurrent()));

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($module->getMaxPower() / 1000);
                    $n_mod = $p * $s;
                    if ($pot >= ($modPower * $percentPower[$i])) {
                        $inverters[$i]["serial"] = (int) $s;
                        $inverters[$i]["parallel"] = (int) $p;
                        break 2;
                    }
                }
            }
        }

        return $inverters;
    }

    private function previewPower($kwh, $latitude, $longitude)
    {
        $lat_nasa = floor($latitude);
        $lon_nasa = floor($longitude);
        $gr_rad_bd = $this->getNasaProvider()->radiationGlobal($lat_nasa, $lon_nasa);
        $gr_rad = array_values($gr_rad_bd);

        $at = $this->getNasaProvider()->airTemperature($lat_nasa, $lon_nasa);

        // POWER ESTIMATOR
        $powerEstimator = new PowerEstimator();
        $powerEstimator
            ->setConsumption($kwh)
            ->setGlobalRadiation($gr_rad)
            ->setAirTemperature($at)
            ->setLatitude($lat_nasa)
            ->setLongitude($lon_nasa)
        ;

        return $powerEstimator->estimate();
    }
}
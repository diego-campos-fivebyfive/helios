<?php
/**
 * Created by PhpStorm.
 * User: mauroandre
 * Date: 08/01/18
 * Time: 10:17
 */
//require "conex.php";

/**
 * TESTED
 * - Generator\Common\Math::factorial()
 * - Generator\Common\Math::combination()
 * - Generator\Inverter\Helper::adjustPower()
 * -
 */

// TODO: Generator\Common\Math::factorial() - TEST OK
function fatorial($num)
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

// TODO: Generator\Common\Math::combination() - TEST OK
function comb_rep($a, $b)
{
    $numerador = $a + $b - 1;
    $cont = $b;
    for ($i = $numerador - 1; $cont > 1; $i--) {
        $numerador *= $i;
        $cont -= 1;
    }
    return $numerador / fatorial($b);
}

// TODO: Generator\Inverter\Helper::adjustPower() - TEST OK
function adjust_desire_power($all_inv, $desire_power, $fdi_max)
{

    $new_desire_power = $desire_power;
    if (count($all_inv) > 0) {
        $reference = $all_inv[0]["nominal_power"] / $fdi_max;
        if ($desire_power < $reference) {
            $new_desire_power = $reference;
        }
    }

    return $new_desire_power;
}

// TODO: Generator\Inverter\Helper::allCombinations() - WAITING
function all_combination($n_elements, $max_elements)
{
    $n_possibilities = comb_rep($n_elements, $max_elements);
    $combination = array();

    $contador = array_fill(0, $max_elements, 0);

    for ($i = 0; $i < $n_possibilities; $i++) {
        $combination[$i] = $contador;

        $contador[$max_elements - 1] += 1;

//        if ($contador[$max_elements - 1] >= $n_elements){
//            $contador[$max_elements - 2] += 1;
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }
//
//        if ($contador[$max_elements - 2] >= $n_elements){
//            $contador[$max_elements - 3] += 1;
//            $contador[$max_elements - 2] = $contador[$max_elements - 3];
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }
//
//        if ($contador[$max_elements - 3] >= $n_elements){
//            $contador[$max_elements - 4] += 1;
//            $contador[$max_elements - 3] = $contador[$max_elements - 4];
//            $contador[$max_elements - 2] = $contador[$max_elements - 3];
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }

        for ($k = 1; $k < $max_elements; $k++) {
            if ($contador[$max_elements - $k] >= $n_elements) {
                $contador[$max_elements - ($k + 1)] += 1;
                for ($j = $k; $j >= 1; $j--) {
                    $contador[$max_elements - $j] = $contador[$max_elements - ($j + 1)];
                }
            }
        }
    }

    return $combination;

}

// TODO: Generator\Inverter\Helper::allCombinationsOptimized() - WAITING
function all_combination_opt($n_elements, $max_elements)
{
    $n_possibilities = comb_rep($n_elements, $max_elements);
    $combination = array();

    for ($i = 0; $i < $n_elements; $i++) {
        $contador = array_fill(0, $max_elements, $i);
        $combination[$i] = $contador;

    }

    $contador = array_fill(0, $max_elements, 0);

    for ($i = $n_elements; $i < $n_possibilities; $i++) {

        $contador[$max_elements - 1] += 1;

//        if ($contador[$max_elements - 1] >= $n_elements){
//            $contador[$max_elements - 2] += 1;
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }
//
//        if ($contador[$max_elements - 2] >= $n_elements){
//            $contador[$max_elements - 3] += 1;
//            $contador[$max_elements - 2] = $contador[$max_elements - 3];
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }
//
//        if ($contador[$max_elements - 3] >= $n_elements){
//            $contador[$max_elements - 4] += 1;
//            $contador[$max_elements - 3] = $contador[$max_elements - 4];
//            $contador[$max_elements - 2] = $contador[$max_elements - 3];
//            $contador[$max_elements - 1] = $contador[$max_elements - 2];
//        }

        for ($k = 1; $k < $max_elements; $k++) {
            if ($contador[$max_elements - $k] >= $n_elements) {
                $contador[$max_elements - ($k + 1)] += 1;
                for ($j = $k; $j >= 1; $j--) {
                    $contador[$max_elements - $j] = $contador[$max_elements - ($j + 1)];
                }
            }
        }

        $test_unique = array_unique($contador);
        if (count($test_unique) == 1) {
            $contador[$max_elements - 1] += 1;
        }

        $combination[$i] = $contador;
    }

    return $combination;

}

// TODO: Generator\Inverter\Helper::filterActives() - WAITING
function inv_active_alternative_filter($all_inv, $all_alternatives)
{
    //ao invés de checar se o status é ACTIVE deve-se verificar se o nível de desconto está presente

    $inv = $all_inv;
    $alt = $all_alternatives;


    $count_inv = count($inv);
    for ($i = 0; $i < $count_inv; $i++) {
        $inv_id = $inv[$i]["id"];
        $active = $inv[$i]["active"];
        $alternative_id = $inv[$i]["alternative"];

        if ($active == 0) {
            unset($inv[$i]);

            if ($alternative_id > 0) {
                $alt_mem = array();

                $index_mem = 1;
                $alt_mem[0] = $alternative_id;

                for ($m = 0; $m < count($alt_mem); $m++) {

                    $procura = array_search($alt_mem[$m], array_column($alt, "id"));
                    $achou = is_numeric($procura);

                    if ($achou) {
                        $is_alt_active = $alt[$procura]["active"];
                        if ($is_alt_active > 0) {
                            $proc_inv = array_search($alt[$procura]["id"], array_column($inv, "id"));
                            if (!is_numeric($proc_inv)){
                                $inv[$i] = $alt[$procura];
                            }

                        } else {
                            $alt_alt_id = $alt[$procura]["alternative"];

                            if ($alt_alt_id > 0) {
                                $proc_alt = array_search($alt_alt_id, $alt_mem);
                                if (!is_numeric($proc_alt)){
                                    $alt_mem[$index_mem] = $alt_alt_id;
                                    $index_mem += 1;
                                }

                            }
                        }

                    }

                }

            }

        }


    }

    uasort($inv, function ($a, $b){
       return $a["nominal_power"] > $b["nominal_power"];
    });

    $inv = array_values($inv);


    return $inv;
}

// TODO: Generator\Inverter\Helper::filterPhases() - WAITING
function inv_phase_filter($all_inv, $phase_voltage, $n_phase)
{

    $inv = $all_inv;
    $net = [$phase_voltage, $n_phase];


    if ($net == [220, 1] or $net == [220, 2]) {
        $cont_inv = count($inv);
        for ($i = 0; $i < $cont_inv; $i++) {
            $np = $inv[$i]["phase_number"];
            if ($np > $n_phase) {
                unset($inv[$i]);
            }
        }
        $inv = array_values($inv);
    }


    if ($net == [380, 3] or $net == [220, 3]) {
        $cont_inv = count($inv);
        for ($i = 0; $i < $cont_inv; $i++) {
            $pv = $inv[$i]["phase_voltage"];
            $co = $inv[$i]["compatibility"];
            if ($pv == $phase_voltage) {
                $co = 0;
            }
            if ($pv > 380 or $co > 0) {
                unset($inv[$i]);
            }
        }
        $inv = array_values($inv);
    }


    if ($phase_voltage > 380) {
        $cont_inv = count($inv);
        for ($i = 0; $i < $cont_inv; $i++) {
            $pv = $inv[$i]["phase_voltage"];
            if ($pv != $phase_voltage) {
                unset($inv[$i]);
            }
        }
        $inv = array_values($inv);
    }

    return $inv;

}

// TODO: Generator\Inverter\Helper::filterPower() - WAITING
function inv_power_filter($all_inv, $desire_power)
{

    $inv = $all_inv;

    $cont_inv = count($inv);
    for ($i = 0; $i < $cont_inv; $i++) {
        $max_show = $inv[$i]["pow_max_show"];
        $min_show = $inv[$i]["pow_min_show"];
        if ($max_show > 0) {
            if ($desire_power < $min_show or $desire_power > $max_show) {
                unset($inv[$i]);
            }
        }
    }
    $inv = array_values($inv);


    if ($desire_power >= 75) {
        $cont_inv = count($inv);
        for ($i = 0; $i < $cont_inv; $i++) {
            $pn = $inv[$i]["phase_number"];
            if ($pn < 3) {
                unset($inv[$i]);
            }
        }
        $inv = array_values($inv);
    }

    if ($desire_power >= 500) {
        $cont_inv = count($inv);
        for ($i = 0; $i < $cont_inv - 1; $i++) {
            unset($inv[$i]);
        }
        $inv = array_values($inv);
    }


    return $inv;

}

// TODO: Generator\Inverter\Helper::inverterChoices() - WAITING
function inv_choice($all_inv, $desire_power, $fdi_min, $fdi_max)
{

    $lim_inf = $desire_power * $fdi_min;
    $lim_sup = $desire_power * $fdi_max;

    $selection = array();

    for ($i = 0; $i <= 30; $i++) {
        $combinations = all_combination_opt(count($all_inv), $i + 1);
        $n_combinations = count($combinations);

        for ($k = 0; $k < $n_combinations; $k++) {
            $acu_pow = 0;
            $n_elements = count($combinations[$k]);

            for ($j = 0; $j < $n_elements; $j++) {
                $index = $combinations[$k][$j];
                $acu_pow += $all_inv[$index]["nominal_power"];

                $selection[$j] = $all_inv[$index];
            }

            if ($acu_pow >= $lim_inf and $acu_pow <= $lim_sup) {
                break 2;
            }

        }
        $selection = array();
    }
    return $selection;
}

// TODO: Generator\Inverter\Helper::powerBalance() - WAITING
function inv_power_balance($all_inv, $desire_power){

    $power_balance = array();

    for ($i=0; $i<count($all_inv); $i++){
        $power_balance[$i] = $all_inv[$i]["nominal_power"];
    }

    $inv_total_power = array_sum($power_balance);

    for ($i=0; $i<count($power_balance); $i++){
        $power_balance[$i] = ($power_balance[$i] / $inv_total_power) * $desire_power;
    }

    return $power_balance;

}

// TODO: Generator\Inverter\Helper::mpptOperation() - WAITING
function inv_get_mppt_op ($all_inv){

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

// TODO: Generator\Inverter\Helper::hasProtection() - WAITING
function inv_get_in_protections($all_inv){

    $in_protection = array();
    for ($i=0; $i<count($all_inv); $i++){
        $in_protection[$i] = $all_inv[$i]["in_protections"];
    }
    return $in_protection;
}

// TODO: Generator\Inverter\Helper::allArrangements() - WAITING
function all_arrangements($inv_selected, $inv_mppt_op, $mod_selected)
{

//    inv_mppt_op = 1 (executa com todos os mppts juntos)
//    inv_mppt_op = 0 (executa com todos os mppts separados)
//    obs: não é de responsabilidade desta função definir se é possível ou não ligar em paralelo os MPPTS do inversor selecionado

    $inv_max_dc_voltage = $inv_selected["max_dc_voltage"];
    $inv_mppt_min = $inv_selected["mppt_min"];
    $inv_mppt_max_dc_current = $inv_selected["mppt_max_dc_current"];
    $inv_mppt_number = $inv_selected["mppt_number"];

    $tnoct = 45;
    $tc_min = 10;
    $tc_max = 70;

    $mod_max_power = $mod_selected["max_power"];
    $mod_open_circuit_voltage = $mod_selected["open_circuit_voltage"];
    $mod_voltage_max_power = $mod_selected["voltage_max_power"];
    $mod_temp_coefficient_voc = $mod_selected["temp_coefficient_voc"];
    $mod_short_circuit_current = $mod_selected["short_circuit_current"];

    $mod_vmax = $mod_open_circuit_voltage;
    $mod_vmin = $mod_voltage_max_power * ((1 + (($tc_max - $tnoct) * ($mod_temp_coefficient_voc / 100))));

    $qty_max_mod_ser = floor($inv_max_dc_voltage / $mod_vmax);
    $qty_min_mod_ser = ceil($inv_mppt_min / $mod_vmin);

    $cont_mppt_area = count($inv_mppt_op);

    $combinations = array();
    for ($i = 0; $i < $cont_mppt_area; $i++) {
        $index = 0;
        $qty_max_mod_par = ($inv_mppt_max_dc_current * $inv_mppt_op[$i]) / $mod_short_circuit_current;
        for ($p = 1; $p <= $qty_max_mod_par; $p++) {
            for ($s = $qty_min_mod_ser; $s <= $qty_max_mod_ser; $s++) {
                $power = $p * $s * $mod_max_power / 1000;
                $combinations[$i][$index] = [
                    "par" => $p,
                    "ser" => $s,
                    "power" => $power
                ];
                $index += 1;
            }
        }
    }
    return $combinations;
}

// TODO: Generator\Inverter\Helper::autoArrangement() - WAITING
function auto_arrangement_choice($all_arrangements, $desired_power, $inv_mppt_op)
{

    $arrangements = $all_arrangements;
    $power = $desired_power;
    $mppt_number = array_sum($inv_mppt_op);

    $choice = array();
    $acu_power = 0;
    for ($i = 0; $i < count($arrangements); $i++) {

        $power_ref = (($power - $acu_power) / $mppt_number) * $inv_mppt_op[$i];
        $dif_mem_power = $power_ref;

        for ($index = 0; $index < count($arrangements[$i]); $index++) {
            $actual_power = $arrangements[$i][$index]["power"];
            $dif_result = abs($power_ref - $actual_power);

            if ($dif_result < $dif_mem_power) {
                $choice[$i] = $arrangements[$i][$index];
                $dif_mem_power = $dif_result;

            }
        }
        $acu_power += $choice[$i]["power"];
        $mppt_number -= $inv_mppt_op[$i];
    }
    return $choice;

}

// TODO: Generator\StringBox\Helper::getParameters() - WAITING
function stringbox_parameters($arrangement_choice)
{

    $inputs = 0;
    for ($i = 0; $i < count($arrangement_choice); $i++) {
        $inputs += $arrangement_choice[$i]["par"];
    }
    $outputs = count($arrangement_choice);

    $parameters = [
        "inputs" => $inputs,
        "outputs" => $outputs
    ];

    return $parameters;
}

// TODO: Generator\StringBox\Helper::getChoices() - WAITING
function stringbox_choice($stringbox_parameters, $all_stringbox)
{

    $inputs = $stringbox_parameters["inputs"];
    $outputs = $stringbox_parameters["outputs"];

    $selection = array();

    for ($i = 0; $i <= 10; $i++) {
        $combinations = all_combination(count($all_stringbox), $i + 1);
        $n_combinations = count($combinations);

        for ($k = 0; $k < $n_combinations; $k++) {
            $acu_in = 0;
            $acu_out = 0;
            $n_elements = count($combinations[$k]);

            for ($j = 0; $j < $n_elements; $j++) {
                $index = $combinations[$k][$j];
                $acu_in += $all_stringbox[$index]["in_qty"];
                $acu_out += $all_stringbox[$index]["out_qty"];

                $selection[$j] = $all_stringbox[$index];
            }

            $test_in = $inputs - $acu_in;
            $test_out = $outputs - $acu_out;

            if (($test_in <= 0) and ($test_out <= 0)) {
                break 2;
            }
        }
        $selection = array();
    }
    return $selection;
}


?>


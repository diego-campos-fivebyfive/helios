<?php
/**
 * Created by PhpStorm.
 * User: mauroandre
 * Date: 22/01/18
 * Time: 11:43
 */
include "funcs2.php";

$maker_inv = 60627;
$all_inv = R::getAll("SELECT * FROM app_component_inverter WHERE maker = $maker_inv ORDER BY nominal_power ASC");
$all_alternatives = R::getAll("SELECT * FROM app_component_inverter WHERE id in (SELECT DISTINCT(alternative) FROM app_component_inverter WHERE alternative > 0) and maker != $maker_inv ORDER BY nominal_power ASC");

$mod_id = 46131;
$mod = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id");


$string_box_maker = 1;
$stb = R::getAll("SELECT * FROM str_box WHERE maker = $string_box_maker ORDER BY in_qty ASC, out_qty ASC");

$desire_power = 12;
$fdi_min = 0.75;
$fdi_max = 1.3;
$phase_voltage = 220;
$n_phase = 1;

$all_inv = inv_active_alternative_filter($all_inv, $all_alternatives);
$all_inv = inv_phase_filter($all_inv, $phase_voltage, $n_phase);
$desire_power = adjust_desire_power($all_inv, $desire_power, $fdi_max);
$all_inv = inv_power_filter($all_inv, $desire_power);
$all_inv = inv_choice($all_inv, $desire_power, $fdi_min, $fdi_max);
$power_balance = inv_power_balance($all_inv, $desire_power);
$mppt_op = inv_get_mppt_op($all_inv);   // Areas
$in_protection = inv_get_in_protections($all_inv);

$arrangements = array();
$string_box = array();
for ($i=0; $i<count($all_inv); $i++){
    $arrangements[$i] = all_arrangements($all_inv[$i], $mppt_op[$i], $mod[0]);
    $arrangements[$i] = auto_arrangement_choice($arrangements[$i], $power_balance[$i], $mppt_op[$i]);
    // Esta função retorna todas as áreas de todos os inversores
    // serial => mod_string
    // parallel => nro_string
    if ($in_protection[$i] == 0){
        $stringbox_parameters = stringbox_parameters($arrangements[$i]);
        $string_box[$i] = stringbox_choice($stringbox_parameters, $stb);
    }
}

print_r($all_inv);
echo "<br><br>";
print_r($arrangements);
echo "<br><br>";
print_r($string_box);


/***
 * PARÂMETROS DE ENTRADA
 *
 *:: MODO AUTOMÁTICO ::
 * - POTÊNCIA
 * - MÓDULO
 * - FAB INVERSOR
 * - FASE
 * -- 220V / 1 FASE
 * -- 220V / 2 FASES
 * -- 220V / 3 FASES
 * -- 380V / 3 FASES
 * -- 380+ / ~ (AGUARDANDO DEFINIÇÃO)
 * - FAB STRING_BOX
 *
 *:: MODO MANUAL ::
 * - MÓDULO
 * - FAB >> INVERSOR (SELECIONA O INVERSOR)
 * - MODO DE OPERAÇÃO DO INVERSOR
 * - FAB STRING_BOX
 */


?>

<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\Support;

/**
 * Class InverterCombiner
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class LegacyInverterCombiner
{
    /**
     * @var float
     */
    private $power;

    /**
     * @var float
     */
    private $minimumFactor;

    /**
     * @var float
     */
    private $maximumFactor;

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param mixed $power
     * @return InverterCombiner
     */
    public function setPower($power)
    {
        $this->power = $power;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumFactor()
    {
        return $this->minimumFactor;
    }

    /**
     * @param mixed $minimumFactor
     * @return InverterCombiner
     */
    public function setMinimumFactor($minimumFactor)
    {
        $this->minimumFactor = $minimumFactor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaximumFactor()
    {
        return $this->maximumFactor;
    }

    /**
     * @param mixed $maximumFactor
     * @return InverterCombiner
     */
    public function setMaximumFactor($maximumFactor)
    {
        $this->maximumFactor = $maximumFactor;
        return $this;
    }

    public function combine(CombinedCollectionInterface $collection)
    {
        $inverters = $collection->getCombinations();
        $combination = 2;

        $module = $collection->getModule();

        $fdi_max = 1;
        $fdi_min = 0.75;
        $lim_sup = $module->getMaxPower() * $fdi_max;
        $lim_inf = $module->getMaxPower() * $fdi_min;

        //$lim_sup = $pot_mod * $fdi_max;
        //$lim_inf = $pot_mod * $fdi_min;
        //$lim_sup = 395.59;
        //$lim_inf = 296.69949316258;
        //var_dump($collection); die;

        for ($i = $combination; $i <= 50; $i++) {

            //var_dump('Ciclo: ' . $i);

            $cont = array_fill(0, $i, 0);
            //$max = count($inverters)-1;
            $max = $collection->count()-1;

            //var_dump(Support::combine($collection->count(), $i)); die;

            for ($j = 0; $j <  Support::combine($collection->count(), $i); $j++){

                $result = 0;
                for ($y = 0; $y < count($cont); $y++) {

                    //var_dump($cont[$y]);

                    /**
                     * TODO
                     * Observado erro de undefined offset ao considerar
                     * inversores com potências nominais abaixo de 5
                     */
                    $result += $inverters[$cont[$y]]->getNominalPower();//["nominal_power"];

                    // var_dump($inverters[$y]->getNominalPower());
                    // echo $busca_inv_bd[$cont[$y]]["model"];
                    // echo "<br>";
                    //var_dump($result."\n");
                }

                //die;

                //echo "$result <br>";
                if ($result <= $lim_sup and $result >= $lim_inf) {
                    break 2;
                }

                $cont[$i - 1] += 1;
                for ($k = 1; $k < $i; $k++) {
                    if ($cont[$i - $k] > $max) {
                        $cont[$i - ($k + 1)] += 1;
                        for ($z = $k; $z >= 1; $z--) {
                            $cont[$i - $z] = $cont[$i - ($z + 1)];
                        }
                    }
                }
            }
        }

        if (count($cont) == 50){
            //$inv_out = null;
            return false;
            die();
        }

        rsort($cont);

        //var_dump($cont); die;

        //echo "<br>";
        $inv_index[0]["inv"] = $cont[0];
        $inv_index[0]["qte"] = 1;
        $contador = 1;
        for ($i = 1; $i < count($cont); $i++) {

            if ($cont[$i] == $cont[$i - 1]) {

                $inv_index[$contador - 1]["qte"] += 1;

                $inverter = $collection->get($contador-1);
                $collection->get($contador-1)->getQuantity();
                $inverter->setQuantity($inverter->getQuantity() + 1);

            } else {

                $inv_index[$contador]["inv"] = $cont[$i];
                $inv_index[$contador]["qte"] = 1;
                $contador += 1;
            }
        }

        return $collection;
    }

    public function __combine($kwh, $lat, $lon, $mod_id)
    {
        /**
         * CRITÉRIO ARBITRÁRIO
         * HÁ UMA ESTRATÉGIA ANTERIOR
         */
        $busca_mod_bd = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id ORDER BY max_power ASC");

//$mod_qte = 600;
//$pot_mod = ($busca_mod_bd[0]["max_power"] * $mod_qte) / 1000;
        $pot_mod = $kw;
//$mod_qte = floor($pot_mod / ($busca_mod_bd[0]["max_power"] / 1000));
//echo "$pot_mod kWp<br>";

        $fdi_max = 1;
        $fdi_min = 0.75;
        $lim_sup = $pot_mod * $fdi_max;
        $lim_inf = $pot_mod * $fdi_min;

        /**
         * AQUI ENTRARÁ O MECANISMO QUE FARÁ UM LOOP PROCURANDO PELO INVERSOR MAIS BARATO
         * COMPATÍVEL COM A POTÊNCIA EXIGIDA
         */
        $inv_index = array();
        $comb = 1;
        $busca_inv_bd = R::getAll("SELECT * FROM app_component_inverter WHERE maker = 60627 AND nominal_power BETWEEN $lim_inf AND $lim_sup ORDER BY nominal_power ASC");
        if (count($busca_inv_bd) > 0) {
            $inv_index[0]["inv"] = 0;
            $inv_index[0]["qte"] = 1;
        }

        if (count($busca_inv_bd) == 0) {
            $comb = 2;
            for ($l = 0.3; $l > 0.025; $l -= 0.015) {
                $busca_inv_bd = R::getAll("SELECT * FROM app_component_inverter WHERE maker = 60627 AND nominal_power BETWEEN $lim_inf*$l AND $lim_sup ORDER BY nominal_power ASC");
                //echo count($busca_inv_bd) . " $l<br>";
                if (count($busca_inv_bd) >= 2) {
                    break;
                }
            }
        }


        ///

// rotina para calcular com n (n >= 2) inversores
        if ($comb == 2 and count($busca_inv_bd) >= 1) {

            // para n inversores iguais
            for ($j = $comb; $j <= 50; $j++) {
//        for ($i=0; $i<count($busca_inv_bd); $i++){
//            $result = $busca_inv_bd[$i]["nominal_power"] * $j;
//            if ($result <= $lim_sup and $result >= $lim_inf){
//                echo $j . "x " . $busca_inv_bd[$i]["model"];
//                break 2;
//            }
//        }

                $cont = array($j);
                //resetando array
                for ($i = 0; $i < $j; $i++) {
                    $cont[$i] = 0;
                }

                $max = count($busca_inv_bd) - 1;
                for ($i = 0; $i < comb_rep(count($busca_inv_bd), $j); $i++) {
//            echo "<br>";
//            print_r($cont);
//            echo "<br>";
                    $result = 0;
                    for ($y = 0; $y < count($cont); $y++) {
                        $result += $busca_inv_bd[$cont[$y]]["nominal_power"];
//                echo $busca_inv_bd[$cont[$y]]["model"];
//                echo "<br>";
                    }
                    //echo "$result <br>";
                    if ($result <= $lim_sup and $result >= $lim_inf) {
                        break 2;
                    }
//                if ($result > $lim_sup){
//                    echo "<br>Nenhuma combinação de inversor encontrada<br>";
//                    break 2;
//                }

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
                //echo "Nenhum inversor encontrado na lista<br>";
            }
            if (count($cont) == 50) {
                //$inv_out = null;
                return false;
                die();
            }
            rsort($cont);
            //echo "<br>";
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
//echo comb_rep(1,2);
//print_r($inv);
//echo "<br>";
        if (count($inv_index) == 0) {
            //$inv_out = null;
            return false;
            die();
        }
//    return $cont;
//    die();

//-------- INICIANDO CÁLCULO DE MÓDULOS COM STRIGS DOS INVERSORES --------
        $pot_total_inv = 0;
        for ($i = 0; $i < count($inv_index); $i++) {
//        $model = $busca_inv_bd[$inv_index[$i]["inv"]]["model"];
//        $qte = $inv_index[$i]["qte"];
            $pot_total_inv += $busca_inv_bd[$inv_index[$i]["inv"]]["nominal_power"] * $inv_index[$i]["qte"];
            //echo "$qte - $model<br>";
        }
//calculando o percentual de potêcia para cada inversor baseada na potência total desejada
        $percent_inv = array();
        for ($i = 0; $i < count($inv_index); $i++) {
            $percent_inv[$i] = $busca_inv_bd[$inv_index[$i]["inv"]]["nominal_power"] / $pot_total_inv;
            //echo "$percent_inv[$i]<br>";
        }
//echo "$pot_total_inv<br>";

        $tnoct = 45;
        $tc_min = 10;
        $tc_max = 70;

        $vmax_mod = $busca_mod_bd[0]["open_circuit_voltage"];
        $vmin_mod = $busca_mod_bd[0]["voltage_max_power"] * (1 + (($tc_max - $tnoct) * ($busca_mod_bd[0]["temp_coefficient_voc"] / 100)));

// calculando número de strings e módulos/string de cada inversor
        for ($i = 0; $i < count($inv_index); $i++) {
            $qte_max_mod_ser = floor($busca_inv_bd[$inv_index[$i]["inv"]]["max_dc_voltage"] / $vmax_mod);
            $qte_min_mod_ser = ceil($busca_inv_bd[$inv_index[$i]["inv"]]["mppt_min"] / $vmin_mod);
            $qte_max_mod_par = floor(($busca_inv_bd[$inv_index[$i]["inv"]]["mppt_max_dc_current"] * $busca_inv_bd[$inv_index[$i]["inv"]]["mppt_number"]) / ($busca_mod_bd[0]["short_circuit_current"]));

            //echo "<br>Max série = $qte_max_mod_ser<br>Min série = $qte_min_mod_ser<br>Max par = $qte_max_mod_par<br><br>";

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($busca_mod_bd[0]["max_power"] / 1000);
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
            $inv_out[$i]["qte"] = $inv_index[$i]["qte"];
            $inv_out[$i]["ser"] = $inv_index[$i]["ser"];
            $inv_out[$i]["par"] = $inv_index[$i]["par"];
        }

        /**
         * APÓS ISSO É POSSÍVEL PRECIFICAR O KIT - OUTRO ALGORITMO
         */
        return $inv_out;

    }
}
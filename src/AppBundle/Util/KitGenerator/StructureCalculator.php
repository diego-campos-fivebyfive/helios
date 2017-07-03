<?php
/**
 * Created by PhpStorm.
 * User: Fabio
 * Date: 20/06/2017
 * Time: 16:34
 */

namespace AppBundle\Util\KitGenerator;


class StructureCalculator
{
    /**
     * @var int
     */
    private $roofType;

    /**
     * @var string
     */
    private $position;

    /**
     * @var int
     */
    private $cellNumber;

    /**
     * @return mixed
     */
    public function getRoofType()
    {
        return $this->roofType;
    }

    /**
     * @param mixed $roofType
     * @return StructureCalculator
     */
    public function setRoofType($roofType)
    {
        $this->roofType = $roofType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     * @return StructureCalculator
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCellNumber()
    {
        return $this->cellNumber;
    }

    /**
     * @param mixed $cellNumber
     * @return StructureCalculator
     */
    public function setCellNumber($cellNumber)
    {
        $this->cellNumber = $cellNumber;
        return $this;
    }


    public function calculate($qte_mod, $tipo_telhado, $posicao, $mod_id){
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

//definição dos dados do módulo
        $mod_bd = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id ORDER BY max_power ASC");
        $n_cell = $mod_bd[0]["cell_number"];
        $mod_comprimento = $mod_bd[0]["length"];
        $mod_largura = $mod_bd[0]["width"];

//setar limetes de módulos por linha na horizontal ou vertical
//para módulos na vertical
        $dim_usada = $mod_largura;
        $mod_lim = 12;
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
//echo $n_cell;
//die();

//quebrando a quantidade de módulos em grupos
        $grupos = array(ceil($total_mod / $mod_lim));
        $resto_mod = $total_mod;
        for ($i = 0; $i < ceil($total_mod / $mod_lim); $i++) {
            $grupos[$i] = $resto_mod;
            if ($resto_mod > $mod_lim) {
                $grupos[$i] = $mod_lim;
                $resto_mod -= $mod_lim;
            }
        }

//------- ACESSANDO BD --------
        $subtipo = "roman";
        if ($tipo_telhado == 3 or $tipo_telhado == 4) {
            $subtipo = "industrial";
        }
        $data2 = R::getAll("SELECT * FROM est_fix WHERE tipo = 'perfil' AND subtipo = '$subtipo' ORDER BY tamanho DESC");
        $term_final_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'terminal' AND subtipo = 'final'");
        $term_inter_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'terminal' AND subtipo = 'intermediario'");

        $ter_final_largura = $term_final_bd[0]["tamanho"];
        $ter_int_largura = $term_inter_bd[0]["tamanho"];

//inicializando quantidades de perfis
        $total_perfil_usado = array(count($data2));
        for ($i = 0; $i < count($data2); $i++) {
            $total_perfil_usado[$i] = 0;
        }

        $total_juncao = 0;
        $total_term_final = 0;
        $total_term_inter = 0;
        $total_perfil_chapa_meio = 0;
        $total_base = 0;
        $total_parafuso_martelo = 0;
        $total_porca_m10 = 0;
        $total_parafuso_est = 0;
        $total_parafuso_auto = 0;
        $total_triangulo = 0;
        $total_fita = 0;


//------- INÍCIO DO CÁLCULO DAS ESTRUTURAS --------

//echo "<table border=1>";
//echo "<tr><td> Nº de módulos </td><td> 6,30 </td><td> 4,20 </td><td> 3,15 </td><td> 2,10 </td><td> 1,57 </td><td> Junções </td><td>Terminal final</td><td>Terminal inter</td></tr>";
        for ($lac = 0; $lac < count($grupos); $lac++) {

            $qte_mod = $grupos[$lac];

            $tam_linha = ($qte_mod * $dim_usada) + (($qte_mod - 1) * $ter_int_largura) + (2 * $ter_final_largura);

            $perfil_usado = array(count($data2));
            //inicializando quantidades de perfis
            for ($i = 0; $i < count($data2); $i++) {
                $perfil_usado[$i] = 0;
            }

            $perfil_usado[$tam_max_perfil] = floor($tam_linha / $data2[$tam_max_perfil]["tamanho"]);
            $resto_opc1 = (($tam_linha / $data2[$tam_max_perfil]["tamanho"]) - $perfil_usado[$tam_max_perfil]) * $data2[$tam_max_perfil]["tamanho"];

            // definição do primeiro maior perfil em relação à resto_opc1
            $primeiro_maior_opc1 = 0;
            for ($i = count($data2) - 1; $i >= $tam_max_perfil; $i--) {
                $primeiro_maior_opc1 = $i;
                if (($resto_opc1 - $data2[$i]["tamanho"]) < 0) {
                    break;
                }
            }

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

            //calculando sobra
            $tamanho_usado = 0;
            for ($i = 0; $i < count($data2); $i++) {
                $tamanho_usado += $perfil_usado[$i] * $data2[$i]["tamanho"];
            }
            $sobra = $tamanho_usado - ($tam_linha * 2);

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
            if (($juncao % 2) != 0) {
                $juncao += 1;
            }
            $juncao -= 2;

            //multiplicando pelo número de linhas de módulos
            for ($i = 0; $i < count($perfil_usado); $i++) {
                $perfil_usado[$i] *= $qte_linhas_mod;
            }
            $juncao *= $qte_linhas_mod;
            $term_final = 4 * $qte_linhas_mod;
            $term_inter = ($qte_mod - 1) * 2 * $qte_linhas_mod;
            $perfil_chapa_meio = ($term_final + $term_inter);
            $base = 2 * (ceil(($tam_linha - (2 * 0.35)) / 1.65) + 1) * $qte_linhas_mod;
            if ($qte_mod == 1) {
                $base = 4 * $qte_linhas_mod;
            }
            $parafuso_martelo = $base;
            $porca_m10 = $base;
            $parafuso_est = $base;
            //$parafuso_auto = ceil(4 * (($tam_linha / 0.4) + 1)) * $qte_linhas_mod;
            $parafuso_auto = 4 * (ceil(($tam_linha) / 0.4) + 1) * $qte_linhas_mod;
            if ($tipo_telhado == 4) {
                $parafuso_auto = $perfil_chapa_meio * 4;
            }
            //$parafuso_auto_meio = $perfil_chapa_meio * 4;
            $triangulo = (ceil(($tam_linha - (2 * 0.35)) / 1.65) + 1) * $qte_linhas_mod;
            //$fita = 2 * (ceil(($tam_linha) / 0.4) + 1) * 0.065 * $qte_linhas_mod;
            $fita = ($parafuso_auto / 2) * 0.1;


            //TOTAIS
            $total_juncao += $juncao;
            for ($i = 0; $i < count($data2); $i++) {
                $total_perfil_usado[$i] += $perfil_usado[$i];
            }
            $total_term_final += $term_final;
            $total_term_inter += $term_inter;
            $total_perfil_chapa_meio += $perfil_chapa_meio;
            $total_base += $base;
            $total_parafuso_martelo += $parafuso_martelo;
            $total_porca_m10 += $porca_m10;
            $total_parafuso_est += $parafuso_est;
            $total_parafuso_auto += $parafuso_auto;
            $total_triangulo += $triangulo;
            $total_fita += $fita;


            //echo "<tr><td>$grupos[$lac]</td><td>$perfil_usado[0]</td><td>$perfil_usado[1]</td><td>$perfil_usado[2]</td><td>$perfil_usado[3]</td><td>$perfil_usado[4]</td><td>$juncao</td><td>$term_final</td><td>$term_inter</td></tr>";

        }
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
                    $est_out[$aponta]["qte"] = $total_perfil_usado[$i];
                    $aponta += 1;
//            $cod = $data2[$i]["codigo"];
//            $desc = $data2[$i]["descricao"];
//            $qte = $total_perfil_usado[$i];
//            echo "<tr><td>$cod</td><td>$desc</td><td>$qte</td></tr>";
                }
            }
        } else {
            $perfil_meio_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'perfil' AND subtipo = 'meio_metro'");
            $est_out[$aponta]["est_id"] = $perfil_meio_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_perfil_chapa_meio;
            $aponta += 1;
//    $cod = $perfil_meio_bd[0]["codigo"];
//    $desc = $perfil_meio_bd[0]["descricao"];
//    $qte = $total_perfil_chapa_meio;
//    echo "<tr><td>$cod</td><td>$desc</td><td>$qte</td></tr>";
        }


//------- ITENS PARA OPÇÃO DE TELHADO 0, 1 e 2--------
        if ($tipo_telhado == 0 or $tipo_telhado == 1 or $tipo_telhado == 2) {
            if ($total_juncao > 0) {
                $juncao_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'juncao'");
                $est_out[$aponta]["est_id"] = $juncao_bd[0]["id"];
                $est_out[$aponta]["qte"] = $total_juncao;
                $aponta += 1;
//        $cod_juncao = $juncao_bd[0]["codigo"];
//        $desc_juncao = $juncao_bd[0]["descricao"];
//        echo "<tr><td>$cod_juncao</td><td>$desc_juncao</td><td>$total_juncao</td></tr>";
            }
            $parafuso_martelo_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'fixador' AND subtipo = 'parafuso'");
            $porca_m10_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'fixador' AND subtipo = 'porca'");
            $est_out[$aponta]["est_id"] = $parafuso_martelo_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_parafuso_martelo;
            $aponta += 1;
            $est_out[$aponta]["est_id"] = $porca_m10_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_porca_m10;
            $aponta += 1;
//    $cod_parafuso = $parafuso_martelo_bd[0]["codigo"];
//    $desc_parafuso = $parafuso_martelo_bd[0]["descricao"];
//    $cod_porca_m10 = $porca_m10_bd[0]["codigo"];
//    $desc_porca_m10 = $porca_m10_bd[0]["descricao"];
//    echo "<tr><td>$cod_parafuso</td><td>$desc_parafuso</td><td>$total_parafuso_martelo</td></tr>";
//    echo "<tr><td>$cod_porca_m10</td><td>$desc_porca_m10</td><td>$total_porca_m10</td></tr>";
        }

        $est_out[$aponta]["est_id"] = $term_final_bd[0]["id"];
        $est_out[$aponta]["qte"] = $total_term_final;
        $aponta += 1;
//$cod_ter_final = $term_final_bd[0]["codigo"];
//$desc_ter_final = $term_final_bd[0]["descricao"];
//echo "<tr><td>$cod_ter_final</td><td>$desc_ter_final</td><td>$total_term_final</td></tr>";
        if ($total_term_inter > 0) {
            $est_out[$aponta]["est_id"] = $term_inter_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_term_inter;
            $aponta += 1;
//    $cod_ter_inter = $term_inter_bd[0]["codigo"];
//    $desc_ter_inter = $term_inter_bd[0]["descricao"];
//    echo "<tr><td>$cod_ter_inter</td><td>$desc_ter_inter</td><td>$total_term_inter</td></tr>";
        }

//------- BASES--------
        if ($tipo_telhado == 0) {
            $subtipo_base = "gancho";
            $qte_base = $total_base;
        } elseif ($tipo_telhado == 1) {
            $subtipo_base = "parafuso_estrutural";
            $qte_base = $total_base;
        } elseif ($tipo_telhado == 2) {
            $subtipo_base = "triangulo_vertical";
            $qte_base = $total_triangulo;
            if ($posicao == 1) {
                $subtipo_base = "triangulo_horizontal";
            }
        } elseif ($tipo_telhado == 3 or $tipo_telhado == 4) {
            $subtipo_base = "parafuso_autoperfurante";
            $qte_base = $total_parafuso_auto;
            $fita_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'base' AND subtipo = 'fita'");
            $est_out[$aponta]["est_id"] = $fita_bd[0]["id"];
            $est_out[$aponta]["qte"] = $total_fita;
            $aponta += 1;
//    $cod_fita = $fita_bd[0]["codigo"];
//    $desc_fita = $fita_bd[0]["descricao"];
//    echo "<tr><td>$cod_fita</td><td>$desc_fita</td><td>$total_fita</td></tr>";
        }
        $base_bd = R::getAll("SELECT * FROM est_fix WHERE tipo = 'base' AND subtipo = '$subtipo_base'");
        $est_out[$aponta]["est_id"] = $base_bd[0]["id"];
        $est_out[$aponta]["qte"] = $qte_base;
        $aponta += 1;

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

        return ($est_out);
    }
}
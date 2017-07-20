<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\Structure\Group;
use AppBundle\Service\ProjectGenerator\Structure\Profile;

class Structure
{
    public static function calculate(Project $project, array $data)
    {
        //dump($project); die;
        /** @var Module $module */
        $module = $project->modules[0];
        //dump($module->groups()); die;
        //$groups = $data[self::GROUPS];
        //$groups = $data['groups'];
        $countModules = 0;
        /** @var Group $group */
        foreach ($module->groups() as $group){
            //dump($group);
            $countModules += $group->count();
        }

        //dump($countModules); die;
        $profiles = $data['profiles'];

        //dump($profiles); die;
        //$profiles = $data[self::PROFILES];
        //$module = $data[self::MODULE];
        //$countModules = $module['quantity'];
        //$roof = $data[self::ROOF];
        $roof = $project->roofType;

        //dump($roof); die;

        uasort($profiles, function(Profile $a, Profile $b){
            return $b->size > $a->size;
        });

        //dump($profiles); die;
        $position = $module->position;
        //$linesOfModules = 1;
        $maxProfileSize = 0;

        if ($countModules <= 52) {
            $maxProfileSize = 1;
            if ($countModules <= 26) {
                $maxProfileSize = 2;
                if ($countModules <= 12) {
                    $maxProfileSize = 3;
                }
            }
        }

        //dump($maxProfileSize); die;

        $cellNumber = $module->cellNumber;
        //$dimension = $module['width'];
        /*$moduleLimit = 12;
        if ($countModules > 52) {
            $moduleLimit = 18;
        }*/

        //dump($cellNumber); die;

        /*if ($position == self::POSITION_HORIZONTAL) {
            $dimension = $module['length'];
            $moduleLimit = 6;
            if ($cellNumber == 60) {
                $moduleLimit = 7;
                if ($countModules > 52) {
                    $moduleLimit = 11;
                }
            }
        }*/

        /*$groups = array(ceil($countModules / $moduleLimit));
        $resto_mod = $countModules;

        for ($i = 0; $i < ceil($countModules / $moduleLimit); $i++) {
            $groups[$i] = $resto_mod;
            if ($resto_mod > $moduleLimit) {
                $groups[$i] = $moduleLimit;
                $resto_mod -= $moduleLimit;
            }
        }*/

        //------- ACESSANDO BD --------
        /*$subtipo = "roman";
        if ($roof == self::ROOF_SHEET_METAL or $roof == self::ROOF_SHEET_METAL_PFM) {
            $subtipo = "industrial";
        }*/

        //$data2 = $data[self::PROFILES];

        $items = $data['items'];

        $term_final_bd = $items[self::TERMINAL_FINAL];
        $term_inter_bd = $items[self::TERMINAL_INTERMEDIARY];

        //$ter_final_largura = (float)$term_final_bd["size"];
        //$ter_int_largura = (float)$term_inter_bd["size"];

        //inicializando quantidades de perfis
        /*$total_perfil_usado  = array(count($data2));
        for ($i = 0; $i < count($data2); $i++) {
            $total_perfil_usado[$i] = 0;
            //print_r($data2[$i]);
        }*/

        $total_perfil_usado = array_fill(0, count($profiles), 0);

        //dump($total_perfil_usado); die;

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

        $terminalIntermediarySize = $term_inter_bd['size'];
        $countProfiles = count($profiles);

        //for ($i = 0; $i < count($groups); $i++) {
        foreach($groups as $i => $group){

            //$quantityModules = $groups[$i];
            $linesOfModules = $groups[$i]['lines'];
            $quantityModules = $groups[$i]['modules'];
            $position = $group['position'];
            $dimension = self::POSITION_VERTICAL ==  $position ? $module['width'] : $module['length'] ;

            $lineSize = ($quantityModules * $dimension) + (($quantityModules - 1) * $terminalIntermediarySize) + (2 * $terminalIntermediarySize);

            $usedProfiles = array_fill(0, $countProfiles, 0);
            $profileSize = $profiles[$maxProfileSize]['size'];

            $usedProfiles[$maxProfileSize] = floor($lineSize / $profileSize);

            $remaining = (($lineSize / $profileSize) - $usedProfiles[$maxProfileSize]) * $profileSize;

            $firstOptionSize = 0;
            for ($j = $countProfiles - 1; $j >= $maxProfileSize; $j--) {
                $firstOptionSize = $j;
                if (($remaining - $profiles[$j]['size']) < 0) {
                    break;
                }
            }

            if (($remaining * 2) > $profiles[$firstOptionSize]['size']) {
                if ($maxProfileSize == $firstOptionSize) {
                    $usedProfiles[$maxProfileSize] = ($usedProfiles[$maxProfileSize] * 2) + 2;
                } else {
                    $usedProfiles[$maxProfileSize] = ($usedProfiles[$maxProfileSize] * 2);
                    $usedProfiles[$firstOptionSize] += 2;
                }
            } else {
                $usedProfiles[$maxProfileSize] *= 2;
                if ($maxProfileSize == $firstOptionSize) {
                    $usedProfiles[$maxProfileSize] += 1;
                } else {
                    $usedProfiles[$firstOptionSize] += 1;
                }
            }

            $usedSize = 0;
            for ($k = 0; $k < $countProfiles; $k++) {
                $usedSize += $usedProfiles[$k] * $profiles[$k]['size'];
            }

            $leftover = $usedSize - ($lineSize * 2);

            if ($leftover > 2) {

                $usedProfiles[$maxProfileSize] -= 1;

                $usedSize = 0;
                for ($k2 = 0; $k2 < $countProfiles; $k2++) {
                    $usedSize += $usedProfiles[$k2] * $profiles[$k2]['size'];
                }

                $leftover = abs($usedSize - ($lineSize * 2));

                $a = 0;
                for ($k3 = $countProfiles - 1; $k3 >= $maxProfileSize; $k3--) {
                    $a = $k3;
                    if (($leftover - $profiles[$k3]['size']) < 0) {
                        break;
                    }
                }

                $baseSize = $profiles[$a]['size'];
                $baseSizeSplit = $baseSize / 2;
                $key = false;
                for ($k4 = 0; $k4 < $countProfiles; $k4++) {
                    if ($profiles[$k4]['size'] == $baseSizeSplit) {
                        $key = $k4;
                    }
                }

                if (!$key) {
                    $profiles[$a] += 1;
                } else {
                    $usedProfiles[$key] += 2;
                }
            }

            $junction = array_sum($usedProfiles);

            if (($junction % 2) != 0) {
                $junction += 1;
            }
            $junction -= 2;

            for ($x1 = 0; $x1 < $countProfiles; $x1++) {
                $usedProfiles[$x1] *= $linesOfModules;
            }

            $junction *= $linesOfModules;
            //$term_final = 4 * $qte_linhas_mod;
            $terminalFinal = 4 * $linesOfModules;
            //$term_inter = ($qte_mod - 1) * 2 * $qte_linhas_mod;
            $terminalIntermediary = ($quantityModules - 1) * 2 * $linesOfModules;
            //$perfil_chapa_meio = ($term_final + $term_inter);
            $profileMiddlePlate = ($terminalFinal + $terminalIntermediary);
            $base = 2 * (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;

            //var_dump($quantityModules); die;

            if ($quantityModules == 1) {
                $base = 4 * $linesOfModules;
            }

            //$parafuso_martelo = $base;
            $screwHammer = $base;
            //$porca_m10 = $base;
            $nutM10 = $base;
            //$parafuso_est = $base;
            $screwStr = $base;

            //$parafuso_auto = ceil(4 * (($tam_linha / 0.4) + 1)) * $qte_linhas_mod;
            //$parafuso_auto = 4 * (ceil(($tam_linha) / 0.4) + 1) * $qte_linhas_mod;
            $screwAuto = 4 * (ceil(($lineSize) / 0.4) + 1) * $linesOfModules;
            //var_dump($screwAuto); die;
            //if ($tipo_telhado == 4) {

            if (self::ROOF_SHEET_METAL_PFM == $roof) {
                //$parafuso_auto = $perfil_chapa_meio * 4;
                $screwAuto = $profileMiddlePlate * 4;
            }

            $speedClip = $screwAuto / 2;
            //$parafuso_auto_meio = $perfil_chapa_meio * 4;
            $triangle = (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;
            //$fita = 2 * (ceil(($tam_linha) / 0.4) + 1) * 0.065 * $qte_linhas_mod;
            $plate = ($screwAuto / 2) * 0.1;

            $total_juncao += $junction;
            for ($z = 0; $z < $countProfiles; $z++) {
                $total_perfil_usado[$z] += $usedProfiles[$z];
            }

            $total_term_final += $terminalFinal;
            $total_term_inter += $terminalIntermediary;
            $total_perfil_chapa_meio += $profileMiddlePlate;
            $total_base += $base;
            $total_parafuso_martelo += $screwHammer;
            $total_porca_m10 += $nutM10;
            $total_parafuso_est += $screwStr;
            $total_parafuso_auto += $screwAuto;
            $total_speedclip += $speedClip;
            $total_triangulo += $triangle;
            $total_fita += $plate;
        }

        $data[self::ITEMS][self::TERMINAL_FINAL]['quantity'] = (int)$total_term_final;
        $data[self::ITEMS][self::TERMINAL_INTERMEDIARY]['quantity'] = (int)$total_term_inter;
        $data[self::ITEMS][self::PROFILE_MIDDLE]['quantity'] = (int)$total_perfil_chapa_meio;
        $data[self::ITEMS][self::BASE_HOOK]['quantity'] = (int)$total_base;
        $data[self::ITEMS][self::FIXER_BOLT]['quantity'] = (int)$total_parafuso_martelo;
        $data[self::ITEMS][self::FIXER_NUT]['quantity'] = (int)$total_porca_m10;
        $data[self::ITEMS][self::BASE_SCREW_FRAME]['quantity'] = (int)$total_parafuso_est;
        $data[self::ITEMS][self::BASE_SCREW_AUTO]['quantity'] = (int)$total_parafuso_auto;
        $data[self::ITEMS][self::BASE_SPEED_CLIP]['quantity'] = (int)$total_speedclip;
        $data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL]['quantity'] = (int)$total_triangulo;
        $data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL]['quantity'] = (int)$total_triangulo;
        $data[self::ITEMS][self::BASE_FRICTION_TAPE]['quantity'] = $total_fita;
        $data[self::ITEMS][self::JUNCTION]['quantity'] = $total_juncao;

        //dump($total_fita); die;

        $totalProfiles = $total_perfil_usado;
        foreach ($totalProfiles as $key => $totalProfile){
            if($totalProfile) {
                $profiles[$key]['quantity'] = (int) $totalProfile;
            }else{
                unset($profiles[$key]);
            }
        }

        $data[self::PROFILES] = $profiles;

        switch ($roof){
            case self::ROOF_ROMAN_AMERICAN:

                unset(
                    $data[self::ITEMS][self::BASE_SCREW_FRAME],
                    $data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL],
                    $data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL],
                    $data[self::ITEMS][self::BASE_SCREW_AUTO],
                    $data[self::ITEMS][self::BASE_FRICTION_TAPE],
                    $data[self::ITEMS][self::BASE_SPEED_CLIP],
                    $data[self::ITEMS][self::PROFILE_MIDDLE]
                );

                breaK;

            case self::ROOF_CEMENT:

                unset(
                    $data[self::ITEMS][self::BASE_HOOK],
                    $data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL],
                    $data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL],
                    $data[self::ITEMS][self::BASE_SCREW_AUTO],
                    $data[self::ITEMS][self::BASE_FRICTION_TAPE],
                    $data[self::ITEMS][self::BASE_SPEED_CLIP],
                    $data[self::ITEMS][self::PROFILE_MIDDLE]
                );

                break;

            case self::ROOF_FLAT_SLAB:

                unset(
                    $data[self::ITEMS][self::BASE_HOOK],
                    $data[self::ITEMS][self::BASE_SCREW_FRAME],
                    $data[self::ITEMS][self::BASE_SCREW_AUTO],
                    $data[self::ITEMS][self::BASE_FRICTION_TAPE],
                    $data[self::ITEMS][self::BASE_SPEED_CLIP],
                    $data[self::ITEMS][self::PROFILE_MIDDLE]
                );

                if(self::POSITION_VERTICAL == $position) {
                    unset($data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL]);
                }else{
                    unset($data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL]);
                }

                break;

            case self::ROOF_SHEET_METAL:

                unset(
                    $data[self::ITEMS][self::BASE_HOOK],
                    $data[self::ITEMS][self::JUNCTION],
                    $data[self::ITEMS][self::FIXER_BOLT],
                    $data[self::ITEMS][self::FIXER_NUT],
                    $data[self::ITEMS][self::PROFILE_MIDDLE],
                    $data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL],
                    $data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL],
                    $data[self::ITEMS][self::BASE_SPEED_CLIP],
                    $data[self::ITEMS][self::BASE_SCREW_AUTO]
                );

                break;

            case self::ROOF_SHEET_METAL_PFM:
                unset(
                    $data[self::ITEMS][self::PROFILES],
                    $data[self::ITEMS][self::JUNCTION],
                    $data[self::ITEMS][self::FIXER_BOLT],
                    $data[self::ITEMS][self::FIXER_NUT],
                    $data[self::ITEMS][self::BASE_TRIANGLE_VERTICAL],
                    $data[self::ITEMS][self::BASE_TRIANGLE_HORIZONTAL],
                    $data[self::ITEMS][self::BASE_HOOK],
                    $data[self::ITEMS][self::BASE_SPEED_CLIP],
                    $data[self::ITEMS][self::BASE_SCREW_FRAME]
                );
                break;
        }
    }
}
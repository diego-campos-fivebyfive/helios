<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\Structure;
use AppBundle\Manager\StructureManager;

/**
 * Class StructureCalculator
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class StructureCalculator
{
    const PROFILE_MIDDLE = 'PROFILE_MIDDLE';
    const TERMINAL_FINAL = 'TERMINAL_FINAL';
    const TERMINAL_INTERMEDIARY = 'TERMINAL_INTERMEDIARY';
    const FIXER_BOLT = 'FIXER_BOLT';
    const FIXER_NUT = 'FIXER_NUT';
    const BASE_HOOK = 'BASE_HOOK';
    const BASE_FRICTION_TAPE = 'BASE_FRICTION_TAPE';
    const BASE_SPEED_CLIP = 'BASE_SPEED_CLIP';
    const BASE_SCREW_FRAME = 'BASE_SCREW_FRAME';
    const BASE_TRIANGLE_VERTICAL = 'BASE_TRIANGLE_VERTICAL';
    const BASE_TRIANGLE_HORIZONTAL = 'BASE_TRIANGLE_HORIZONTAL';
    const BASE_SCREW_AUTO = 'BASE_SCREW_AUTO';
    const JUNCTION = 'JUNCTION';

    /**
     * @var array
     */
    private $mappingCriteria = [
        self::TERMINAL_FINAL => ['type' => 'terminal', 'subtype' => 'final'],
        self::TERMINAL_INTERMEDIARY => ['type' => 'terminal', 'subtype' => 'intermediario'],
        self::FIXER_BOLT => ['type' => 'fixador', 'subtype' => 'parafuso'],
        self::FIXER_NUT => ['type' => 'fixador', 'subtype' => 'porca'],
        self::BASE_HOOK => ['type' => 'base', 'subtype' => 'gancho'],
        self::BASE_FRICTION_TAPE => ['type' => 'base', 'subtype' => 'fita'],
        self::BASE_SPEED_CLIP => ['type' => 'base', 'subtype' => 'speedclip'],
        self::PROFILE_MIDDLE => ['type' => 'perfil', 'subtype' => 'meio_metro'],
        self::JUNCTION => ['type' => 'juncao'],
        self::BASE_SCREW_FRAME => ['type' => 'base', 'subtype' => 'parafuso_estrutural'],
        self::BASE_SCREW_AUTO => ['type' => 'base', 'subtype' => 'parafuso_autoperfurante'],
        self::BASE_TRIANGLE_VERTICAL => ['type' => 'base', 'subtype' => 'triangulo_vertical'],
        self::BASE_TRIANGLE_HORIZONTAL => ['type' => 'base', 'subtype' => 'triangulo_horizontal']
    ];

    /**
     * @var StructureManager
     */
    private $manager;

    function __construct(StructureManager $manager)
    {
        $this->manager = $manager;
    }

    public function calculate(ProjectInterface $project)
    {
        /** @var \AppBundle\Entity\Component\StructureInterface[] $profiles */
        $profiles = $this->findStructure(['type' => 'perfil', 'subtype' => 'roman'], false);

        $items = [];
        foreach ($this->mappingCriteria as $field => $criteria) {
            $items[$field] = $this->findStructure($criteria);
        }

        //dump($items); die;
        //$base = $this->filterBase($project->getRoofType());

        /** @var \AppBundle\Entity\Component\ProjectModuleInterface $projectModule */
        $projectModule = $project->getProjectModules()->first();
        $countModules = 0;
        foreach ($projectModule->getGroups() as $group) {
            $countModules += $group['lines'] * $group['modules'];
        }

        $roof = $project->getRoofType();

        uasort($profiles, function (Structure $a, Structure $b) {
            return $b->getSize() > $a->getSize();
        });

        $position = $projectModule->getPosition();
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

        //$items = $project->items;
        $term_inter_bd = $items[self::TERMINAL_INTERMEDIARY];
        $total_perfil_usado = array_fill(0, count($profiles), 0);

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

        $terminalIntermediarySize = $term_inter_bd->getSize();
        $countProfiles = count($profiles);
        $dimension = 0 == $projectModule->getPosition() ? $projectModule->getModule()->getWidth() : $projectModule->getModule()->getLength();

        foreach ($projectModule->getGroups() as $i => $group) {

            $linesOfModules = $group['lines'];
            $quantityModules = $group['modules'];
            $position = $group['position'];

            $lineSize = ($quantityModules * $dimension) + (($quantityModules - 1) * $terminalIntermediarySize) + (2 * $terminalIntermediarySize);

            $usedProfiles = array_fill(0, $countProfiles, 0);
            $profileSize = $profiles[$maxProfileSize]->getSize();

            $usedProfiles[$maxProfileSize] = floor($lineSize / $profileSize);

            $remaining = (($lineSize / $profileSize) - $usedProfiles[$maxProfileSize]) * $profileSize;

            $firstOptionSize = 0;
            for ($j = $countProfiles - 1; $j >= $maxProfileSize; $j--) {
                $firstOptionSize = $j;
                if (($remaining - $profiles[$j]->getSize()) < 0) {
                    break;
                }
            }

            if (($remaining * 2) > $profiles[$firstOptionSize]->getSize()) {
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
                $usedSize += $usedProfiles[$k] * $profiles[$k]->getSize();
            }

            $leftover = $usedSize - ($lineSize * 2);

            if ($leftover > 2) {

                $usedProfiles[$maxProfileSize] -= 1;

                $usedSize = 0;
                for ($k2 = 0; $k2 < $countProfiles; $k2++) {
                    $usedSize += $usedProfiles[$k2] * $profiles[$k2]->getSize();
                }

                $leftover = abs($usedSize - ($lineSize * 2));

                $a = 0;
                for ($k3 = $countProfiles - 1; $k3 >= $maxProfileSize; $k3--) {
                    $a = $k3;
                    if (($leftover - $profiles[$k3]->getSize()) < 0) {
                        break;
                    }
                }

                $baseSize = $profiles[$a]->getSize();
                $baseSizeSplit = $baseSize / 2;
                $key = false;
                for ($k4 = 0; $k4 < $countProfiles; $k4++) {
                    if ($profiles[$k4]->getSize() == $baseSizeSplit) {
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
            $terminalFinal = 4 * $linesOfModules;
            $terminalIntermediary = ($quantityModules - 1) * 2 * $linesOfModules;
            $profileMiddlePlate = ($terminalFinal + $terminalIntermediary);
            $base = 2 * (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;

            if ($quantityModules == 1) {
                $base = 4 * $linesOfModules;
            }

            $screwHammer = $base;
            $nutM10 = $base;
            $screwStr = $base;
            $screwAuto = 4 * (ceil(($lineSize) / 0.4) + 1) * $linesOfModules;

            if (4 == $roof) {
                $screwAuto = $profileMiddlePlate * 4;
            }

            $speedClip = $screwAuto / 2;
            $triangle = (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;
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

        $structures = [];

        $totalProfiles = $total_perfil_usado;
        foreach ($totalProfiles as $key => $totalProfile) {
            if ($totalProfile) {

                /*$projectStructure = new ProjectStructure();
                $projectStructure
                    ->setStructure($profiles[$key])
                    ->setProject($project)
                    ->setQuantity((int)$totalProfile);*/

                $structures[] = [
                    'structure' => $profiles[$key],
                    'quantity' => (int) $totalProfile
                ];
            }
        }

        switch ($project->getRoofType()) {
            case 0:
                $baseStructure = $items[self::BASE_HOOK];
                $structures[] = ['quantity' => (int) $total_base, 'structure' => $baseStructure];
                break;
            case 1:
                $baseStructure = $items[self::BASE_SCREW_FRAME];
                $structures[] = ['quantity' => (int) $total_base, 'structure' => $baseStructure];
                break;

            case 2:
                $baseStructure = 0 == $position ? $items[self::BASE_TRIANGLE_VERTICAL] : $items[self::BASE_TRIANGLE_HORIZONTAL];
                $structures[] = ['quantity' => (int) $total_term_inter, 'structure' => $baseStructure];
                break;

            case 3:
            case 4:
                $baseStructure = $items[self::BASE_SCREW_AUTO];
                $structures[] = ['quantity' => (int) $total_parafuso_auto, 'structure' => $baseStructure];

                $subBaseStructure = $items[self::BASE_FRICTION_TAPE];
                $subBaseQuantity = $total_fita;

                if('SICES' != $project->getStructureType()){
                    $subBaseStructure = $items[self::BASE_SPEED_CLIP];
                    $subBaseQuantity = $total_speedclip;
                }

                $structures[] = ['quantity' => (int) $subBaseQuantity, 'structure' => $subBaseStructure];

                break;
        }

        foreach ($structures as $config){
            $projectStructure = new ProjectStructure();
            $projectStructure
                ->setStructure($config['structure'])
                ->setQuantity($config['quantity'])
                ->setProject($project)
            ;
        }
    }

    /**
     * TODO: ADD MAKER FIELD CRITERIA HERE!
     * @param array $criteria
     * @param bool $single
     * @return mixed
     */
    public function findStructure(array $criteria, $single = true)
    {
        $method = $single ? 'findOneBy' : 'findBy';

        return $this->manager->$method($criteria);
    }
}
<?php

namespace AppBundle\Service\StructureCalculator;

use AppBundle\Entity\Component\ProjectModuleInterface;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\StructureInterface;
use AppBundle\Manager\ProjectStructureManager;
use AppBundle\Manager\StructureManager;
use AppBundle\Manager\ProjectManager;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Util\KitGenerator\StructureCalculator as Calculator;

class StructureCalculator
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var StructureManager
     */
    private $structureManager;

    /**
     * @var ProjectStructureManager
     */
    private $projectStructureManager;

    /**
     * @var array
     */
    private $mappingCriteria = [
        Calculator::TERMINAL_FINAL => ['type' => 'terminal', 'subtype' => 'final'],
        Calculator::TERMINAL_INTERMEDIARY => ['type' => 'terminal', 'subtype' => 'intermediario'],
        Calculator::FIXER_BOLT => ['type' => 'fixador', 'subtype' => 'parafuso'],
        Calculator::FIXER_NUT => ['type' => 'fixador', 'subtype' => 'porca'],
        Calculator::BASE_HOOK => ['type' => 'base', 'subtype' => 'gancho'],
        Calculator::BASE_FRICTION_TAPE => ['type' => 'base', 'subtype' => 'fita'],
        Calculator::BASE_SPEED_CLIP => ['type' => 'base', 'subtype' => 'speedclip'],
        Calculator::PROFILE_MIDDLE => ['type' => 'perfil', 'subtype' => 'meio_metro'],
        Calculator::JUNCTION => ['type' => 'juncao'],
        Calculator::BASE_SCREW_FRAME => ['type' => 'base', 'subtype' => 'parafuso_estrutural'],
        Calculator::BASE_SCREW_AUTO => ['type' => 'base', 'subtype' => 'parafuso_autoperfurante'],
        Calculator::BASE_TRIANGLE_VERTICAL => ['type' => 'base', 'subtype' => 'triangulo_vertical'],
        Calculator::BASE_TRIANGLE_HORIZONTAL => ['type' => 'base', 'subtype' => 'triangulo_horizontal']
    ];

    /**
     * StructureCalculator constructor.
     * @param ProjectManager $projectManager
     * @param StructureManager $structureManager
     */
    public function __construct(ProjectManager $projectManager, StructureManager $structureManager, ProjectStructureManager $projectStructureManager)
    {
        $this->projectManager = $projectManager;
        $this->structureManager = $structureManager;
        $this->projectStructureManager = $projectStructureManager;
    }

    /**
     * @param ProjectInterface $project
     */
    public function calculate(ProjectInterface $project)
    {
        $this->resetStructures($project);

        $projectModule = $project->getProjectModules()->first();

        if(!$projectModule->getGroups()) {
            $this->generateGroups($projectModule);
        }

        $groups = $projectModule->getGroups();

        /** @var \AppBundle\Entity\Component\ModuleInterface $module */
        $module = $projectModule->getModule();
        $profiles = $this->findStructure(['type' => 'perfil', 'subtype' => 'roman'], false);

        $data = [
            Calculator::ROOF => $project->getRoofType(),
            Calculator::MODULE => [
                'cell_number' => $module->getCellNumber(),
                'length' => 1.65,   //$module->getLength(),
                'width' => 0.992,   //$module->getWidth(),
                'quantity' => $projectModule->getQuantity(),
                'position' => 0 == $groups[0]['position'] ? Calculator::POSITION_VERTICAL : Calculator::POSITION_HORIZONTAL
            ],
            Calculator::PROFILES => $profiles,
            Calculator::GROUPS => $groups
        ];

        foreach ($this->mappingCriteria as $field => $criteria){
            $data[Calculator::ITEMS][$field] = $this->findStructure($criteria);
        }

        Calculator::calculate($data);

        foreach($data[Calculator::ITEMS] as $item){
            $this->createProjectStructure($project, $item['entity'], $item['quantity']);
        }

        foreach ($data[Calculator::PROFILES] as $profile){
            $this->createProjectStructure($project, $profile['entity'], $profile['quantity']);
        }

        $this->projectManager->save($project);
    }

    public function loadItems()
    {
        $items = [];
        foreach ($this->mappingCriteria as $field => $criteria){
            //$data[Calculator::ITEMS][$field] = $this->findStructure($criteria);
            $items[$field] = $this->findStructure($criteria);
        }

        return $items;
    }

    /**
     * @param array $criteria
     * @param bool $single
     * @return mixed
     */
    public function findStructure(array $criteria, $single = true)
    {
        $method = $single ? 'findOneBy' : 'findBy';

        /** @var \AppBundle\Entity\Component\StructureInterface $structure */
        $structure = $this->structureManager->$method($criteria);

        if($single){
            return $this->formatStructure($structure);
        }else{

            $structures = [];
            foreach($structure as $item){
                $structures[] = $this->formatStructure($item);
            }

            return $structures;
        }
    }

    /**
     * @param StructureInterface $structure
     * @return array
     */
    private function formatStructure(StructureInterface $structure)
    {
        return [
            'id' => $structure->getId(),
            'code' => $structure->getCode(),
            'description' => $structure->getDescription(),
            'size' => $structure->getSize(),
            'quantity' => 0,
            'entity' => $structure
        ];
    }

    /**
     * @param ProjectInterface $project
     * @param StructureInterface $structure
     * @param $quantity
     */
    private function createProjectStructure(ProjectInterface $project, StructureInterface $structure, $quantity){

        $projectStructure = new ProjectStructure();

        $projectStructure
            ->setProject($project)
            ->setStructure($structure)
            ->setQuantity($quantity)
        ;
    }

    /**
     * @param ProjectInterface $project
     */
    private function resetStructures(ProjectInterface $project)
    {
        $projectStructures = $project->getProjectStructures();
        foreach($projectStructures as $projectStructure){
            $project->removeProjectStructure($projectStructure);
            $this->projectStructureManager->delete($projectStructure, !$projectStructures->next());
        }
    }

    /**
     * @param ProjectModuleInterface $projectModule
     */
    private function generateGroups(ProjectModuleInterface $projectModule)
    {
        $quantity = $projectModule->getQuantity();
        $position = $projectModule->getPosition();

        $limit = $position == Calculator::POSITION_VERTICAL ? 20 : 12 ;

        $groups = array();
        if (0 != ($quantity % $limit) && ($quantity > $limit)) {
            $groups[0]['lines'] = (int) floor($quantity / $limit);
            $groups[0]['modules'] = $limit;
            $groups[0]['position']      = $position;
            $groups[1]['lines'] = 1;
            $groups[1]['modules'] = (int) (($quantity / $limit) - floor($quantity / $limit)) * $limit;
            $groups[1]['position']      = $position;
        } else {
            $groups[0]['lines'] = (int) ceil($quantity / $limit);
            $groups[0]['modules'] = (int) $quantity / ceil($quantity / $limit);
            $groups[0]['position'] = $position;
        }

        $projectModule->setGroups($groups);
    }
}
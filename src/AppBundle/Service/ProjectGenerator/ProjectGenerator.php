<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectExtra;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Service\ProjectProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProjectGenerator
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class ProjectGenerator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @var bool
     */
    private $autoSave;

    /**
     * @var \AppBundle\Manager\ProjectManager
     */
    private $manager;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->project = $this->manager('project')->create();
        $this->autoSave = true;
    }

    /**
     * @param $autoSave
     * @return $this
     */
    public function autoSave($autoSave)
    {
        $this->autoSave = (bool) $autoSave;

        return $this;
    }

    /**
     * @return array
     */
    public function loadDefaults(array $defaults = [])
    {
        return array_merge([
            'latitude' => null,
            'longitude' => null,
            'roof_type' => 'ROOF_ROMAN_AMERICAN',
            'source' => 'consumption',
            'power' => 0,
            'consumption' => 0,
            'module' => 32433,
            'inverter_maker' => 60627,
            'structure_maker' => 61211,
            'string_box_maker' => 61209
        ], $defaults);
    }

    /**
     * @param ProjectInterface|null $project
     * @return ProjectInterface|mixed|object
     */
    public function generate(ProjectInterface $project = null)
    {
        $this->autoSave = false;

        if($project) $this->project = $project;

        if(!$this->project) $this->exception('The project is undefined');

        if(empty($this->project->getDefaults()))  $this->exception('The project defaults is empty');

        $defaults = $this->project->getDefaults();

        // ESTIMATE POWER
        if('consumption' == $defaults['source']){
            $estimator = $this->container->get('power_estimator');
            $power = $estimator->estimate($defaults['consumption'], $defaults['latitude'], $defaults['longitude']);
            $defaults['power'] = $power;
            $this->project->setDefaults($defaults);
        }

        // MODULES
        $this->generateModules($this->project);

        // INVERTERS
        $this->generateInverters($this->project);

        // AREAS
        $this->generateAreas($this->project);

        // HANDLE AREAS
        if($this->project->getLatitude() && $this->project->getLongitude()) {
            $this->handleAreas($this->project);
        }

        // STRUCTURES
        $this->generateStructures($this->project);

        // CABLES AND CONNECTORS
        $this->generateVarieties($this->project);

        // STRING BOXES
        $this->generateStringBoxes($this->project);

        // SYNCHRONIZE DEFAULTS
        $defaults['power'] = $this->project->getPower();
        $this->project->setDefaults($defaults);

        // SAVING
        $this->autoSave = true;
        $this->save($this->project);

        return $this->project;
    }

    /**
     * @param ProjectInterface|null $project
     */
    public function project(ProjectInterface $project = null)
    {
        if(!$project)
            return $this->project;

        if(empty($project->getDefaults()))
            $this->exception('The project defaults is undefined');

        $this->project = $project;

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function process(ProjectInterface $project)
    {
        if(!$project->isComputable()){
            $this->exception('Project is not computable. Call the getChecklist() method to view the status');
        }

        /** @var ProjectProcessor $processor */
        $processor = $this->container->get('app.project_processor');
        $processor->process($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function pricing(ProjectInterface $project)
    {
        /** @var \AppBundle\Manager\Pricing\RangeManager $manager */
        $manager = $this->manager('range');
        $precifier = new Precifier($manager);

        $precifier->priceCost($project);

        /** @var \AppBundle\Entity\Component\PricingManager $pricingManager */
        $pricingManager = $this->container->get('app.kit_pricing_manager');
        $precifier->priceSale($project, $pricingManager);

        //FinancialAnalyzer::analyze($project);
        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateModules(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();
        $module = $this->manager('module')->find($defaults['module']);
        $position = 0;

        $projectModule = new ProjectModule();
        $projectModule
            ->setModule($module)
            ->setProject($this->project)
            ->setPosition($position)
        ;

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @param MakerInterface $maker
     * @return $this
     */
    public function generateInverters(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        $maker = $this->manager('maker')->find($defaults['inverter_maker']);

        $this->resetInverters($project);

        /** @var \AppBundle\Manager\InverterManager $manager */
        $manager = $this->manager('inverter');

        $loader = new InverterLoader($manager);

        $power = (float) $defaults['power'];
        $inverters = $loader->load($power, $maker);
        $project->setInfPower($power);

        foreach ($inverters as $inverter){

            $quantity = $inverter->quantity;

            if(!$inverter instanceof InverterInterface) {
                $inverter = $manager->find($inverter->id);
            }

            for($i = 0; $i < $quantity; $i++) {

                $projectInverter = new ProjectInverter();
                $projectInverter
                    ->setInverter($inverter)
                    ->setQuantity(1);

                $project->addProjectInverter($projectInverter);
            }
        }

        // INVERTER COMBINATIONS
        Combiner::combine($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateAreas(ProjectInterface $project)
    {
        $this->resetAreas($project);

        $manager = $this->manager('project_area');

        $latitude = $project->getLatitude();
        $longitude = $project->getLongitude();
        $inclination = (int) abs($latitude);
        $orientation = $longitude < 0 ? 0 : 180;
        $projectModule = $project->getProjectModules()->first();
        $projectInverters = $project->getProjectInverters();

        foreach ($projectInverters as $projectInverter){

            $projectInverter->getProjectAreas()->clear();

            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();
            $mppt = $inverter->getMpptNumber();

            $mppts = explode('.', $projectInverter->getOperation());

            foreach ($mppts as $item) {
                for ($i = 0; $i < $projectInverter->getQuantity(); $i++) {

                    $this->createArea(
                        $projectInverter,
                        $projectModule,
                        $inclination,
                        $orientation,
                        $projectInverter->getParallel(),
                        $projectInverter->getSerial()
                    );
                }
            }

            $projectInverter
                ->setLoss(10)
                ->setOperation($mppt);
        }

        $this->generateGroups($project);

        $this->save($project);

        return $this;
    }

    public function generateGroups(ProjectInterface $project)
    {
        $projectModule = $project->getProjectModules()->first();

        $quantity = 0;
        foreach ($project->getAreas() as $projectArea){
            $quantity += $projectArea->countModules();
        }

        $position = $projectModule->getPosition();
        $limit = $position == 0 ? 20 : 12 ;

        $groups = [];
        if (0 != ($quantity % $limit) && ($quantity > $limit)) {

            $groups[] = [
                'lines' => (int) (floor($quantity / $limit)),
                'modules' => (int) $limit,
                'position' => $position
            ];

            $groups[] = [
                'lines' => 1,
                'modules' => (int) (round((($quantity / $limit) - (floor($quantity / $limit))) * $limit)),
                'position' => $position
            ];

        } else {

            $groups[] = [
                'lines' => ((int) ceil($quantity / $limit)),
                'modules' => (int) ($quantity / ceil($quantity / $limit)),
                'position' => $position
            ];
        }

        $projectModule
            ->setGroups($groups)
            ->setQuantity($quantity)
        ;

        $this->save($project);

        return $this;
    }

    public function generateAreasViaProjectInverter(ProjectInverter $projectInverter)
    {
        $manager = $this->manager('project_area');
        $areas = $projectInverter->getProjectAreas();
        $count = $areas->count();
        foreach ($areas as $key => $projectArea){
            $manager->delete($projectArea, ($key == $count-1));
        }

        $project = $projectInverter->getProject();
        $latitude = $project->getLatitude();
        $longitude = $project->getLongitude();
        $inclination = (int) abs($latitude);
        $orientation = $longitude < 0 ? 0 : 180;

        $projectModule = $project->getProjectModules()->first();
        $projectInverter->getProjectAreas()->clear();

        $mppts = explode('.', $projectInverter->getOperation());

        foreach ($mppts as $item) {

            $stringNumber = floor($projectInverter->getParallel() / count($mppts));
            $moduleString = $projectInverter->getSerial();

            if($stringNumber <= 0){
                $stringNumber = 1;
            }

            for ($i = 0; $i < $projectInverter->getQuantity(); $i++) {

                $projectArea = $this->createArea(
                    $projectInverter,
                    $projectModule,
                    $inclination,
                    $orientation,
                    $stringNumber,
                    $moduleString
                );

                $manager->save($projectArea);
            }
        }
    }

    /**
     * @param ProjectInverter $projectInverter
     * @param ProjectModule $projectModule
     * @param $inclination
     * @param $orientation
     * @param $stringNumber
     * @param $moduleString
     * @return ProjectArea
     */
    public function createArea(
        ProjectInverter $projectInverter,
        ProjectModule $projectModule,
        $inclination,
        $orientation,
        $stringNumber,
        $moduleString
    ){

        $projectArea = new ProjectArea();
        $projectArea
            ->setProjectInverter($projectInverter)
            ->setProjectModule($projectModule)
            ->setInclination($inclination)
            ->setOrientation($orientation)
            ->setStringNumber($stringNumber)
            ->setModuleString($moduleString)
        ;

        return $projectArea;
    }

    /**
     * @param ProjectInterface $project
     */
    public function handleAreas(ProjectInterface $project)
    {
        /** @var \AppBundle\Entity\Project\NasaProvider $provider */
        $provider = $this->container->get('app.nasa_provider');
        $handler = new AreaHandler($provider);

        foreach ($project->getAreas() as $projectArea){
            $handler->handle($projectArea);
        }

        $this->save($project);

        return $this;
    }

    /**
     * Sync Project Configuration
     * 1. Resolve modules quantity by config string areas
     *
     * @param ProjectInterface $project
     */
    public function synchronize(ProjectInterface $project)
    {
        $countModules = [];
        /** @var \AppBundle\Entity\Component\ProjectAreaInterface $area */
        foreach($project->getAreas() as $area){
            if(null != $projectModule = $area->getProjectModule()) {
                $module = $projectModule->getModule();

                if(!array_key_exists($module->getId(), $countModules))
                    $countModules[$module->getId()] = 0;

                $countModules[$module->getId()] += $area->countModules();
            }
        }

        foreach($project->getProjectModules() as $projectModule){
            if(array_key_exists($projectModule->getModule()->getId(), $countModules)) {
                $projectModule->setQuantity(
                    $countModules[$projectModule->getModule()->getId()]
                );
            }
        }

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateStructures(ProjectInterface $project)
    {
        $this->resetStructures($project);

        /** @var \AppBundle\Manager\StructureManager $manager */
        $manager = $this->manager('structure');
        $calculator = new StructureCalculator($manager);
        $calculator->calculate($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateVarieties(ProjectInterface $project)
    {
        $this->resetVarieties($project);

        /** @var \AppBundle\Manager\VarietyManager $manager */
        $manager = $this->manager('variety');
        $calculator = new VarietyCalculator($manager);
        $calculator->calculate($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateStringBoxes(ProjectInterface $project)
    {
        $this->resetStringBoxes($project);

        /** @var \AppBundle\Manager\StringBoxManager $manager */
        $manager = $this->manager('string_box');
        $loader = new StringBoxLoader($manager);
        $calculator = new StringBoxCalculator($loader);
        $calculator->calculate($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetInverters(ProjectInterface $project)
    {
        $manager = $this->manager('project_inverter');
        foreach ($project->getProjectInverters() as $projectInverter){
            $project->removeProjectInverter($projectInverter);
            $manager->delete($projectInverter, !$project->getProjectInverters()->next());
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetModules(ProjectInterface $project)
    {
        $manager = $this->manager('project_module');
        foreach ($project->getProjectModules() as $projectModule){
            $project->removeProjectModule($projectModule);
            $manager->delete($projectModule, !$project->getProjectModules()->next());
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetAreas(ProjectInterface $project)
    {
        $manager = $this->manager('project_area');
        $projectAreas = $project->getAreas();

        $count = $projectAreas->count();
        foreach ($project->getAreas() as $key => $projectArea){
            $manager->delete($projectArea, ($key == $count-1));
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetStructures(ProjectInterface $project)
    {
        $manager = $this->manager('project_structure');
        foreach ($project->getProjectStructures() as $projectStructure){
            $project->removeProjectStructure($projectStructure);
            $manager->delete($projectStructure, !$project->getProjectStructures()->next());
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetVarieties(ProjectInterface $project)
    {
        $manager = $this->manager('project_variety');
        foreach ($project->getProjectVarieties() as $projectVariety){
            $project->removeProjectVariety($projectVariety);
            $manager->delete($projectVariety, !$project->getProjectVarieties()->next());
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function resetStringBoxes(ProjectInterface $project)
    {
        $manager = $this->manager('project_string_box');
        foreach ($project->getProjectStringBoxes() as $projectStringBox){
            $project->removeProjectStringBox($projectStringBox);
            $manager->delete($projectStringBox, !$project->getProjectStringBoxes()->next());
        }

        return $this;
    }

    /**
     * @param array $data
     * @return ProjectInterface
     */
    public function fromArray(array $data)
    {
        $inverters = $this->findByIds('inverter', array_keys($data['inverters']));
        $modules = $this->findByIds('module', array_keys($data['modules']));
        $stringBoxes = $this->findByIds('stringBox', array_keys($data['string_boxes']));

        $structures = [];
        if($this->hasData($data, 'structures')) {
            $structures = $this->findByIds('structure', array_keys($data['structures']));
        }

        $extras = [];
        if($this->hasData($data, 'extras')) {
            $extras = $this->findByIds('extra', array_keys($data['extras']));
        }

        $manager = $this->manager('project');

        /** @var \AppBundle\Entity\Component\ProjectInterface $project */
        $project = $this->project ? $this->project : $manager->create();

        foreach($inverters as $inverter){
            $projectInverter = new ProjectInverter();
            $projectInverter
                ->setQuantity($data['inverters'][$inverter->getId()])
                ->setProject($project)
                ->setInverter($inverter)
            ;
        }

        foreach ($modules as $module){
            $projectModule = new ProjectModule();
            $projectModule
                ->setQuantity($data['modules'][$module->getId()])
                ->setProject($project)
                ->setModule($module)
            ;
        }

        foreach($stringBoxes as $stringBox){
            $projectStringBox = new ProjectStringBox();
            $projectStringBox
                ->setQuantity($data['string_boxes'][$stringBox->getId()])
                ->setProject($project)
                ->setStringBox($stringBox)
            ;
        }

        foreach($structures as $structure){

            $projectStructure = new ProjectStructure();
            $projectStructure
                ->setQuantity($data['structures'][$structure->getId()])
                ->setProject($project)
                ->setStructure($structure)
            ;

        }

        foreach ($extras as $extra) {

            $projectExtra = new ProjectExtra();
            $projectExtra
                ->setQuantity($data['extras'][$extra->getId()])
                ->setProject($project)
                ->setExtra($extra)
            ;
        }

        // TODO - CHECK PROPERTIES
        foreach ($data as $property => $value){
            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);
            if(method_exists($project, $getter)) {
                if (!$project->$getter()) {
                    $project->$setter($value);
                }
            }
        }
        
        $manager->save($project);

        return $project;
    }

    /**
     * @param array $data
     * @return \AppBundle\Entity\Component\ProjectInterface
     */
    public function fromCombination(array $data)
    {
        $module = $data['module'];
        $inverterData = [];
        $moduleCount  = 0;
        foreach ($data['inverters'] as $inverter){
            $inverterData[$inverter['id']] = $inverter['quantity'];
            $moduleCount += ($inverter['serial'] * $inverter['parallel'] * $inverter['quantity']);
        }

        $stringBoxes = [];
        foreach ($data['string_boxes'] as $stringBox){
            $stringBoxes[$stringBox['id']] = $stringBox['quantity'];
        }

        return $this->fromArray([
            'inverters' => $inverterData,
            'string_boxes' => $stringBoxes,
            'modules' => [
                $module->getId() => $moduleCount
            ],
            'structures' => [],
            'number' => rand(35, 258),
            //'address' => 'The Address',
            //'latitude' => $data['latitude'],
            //'longitude' => $data['longitude']
        ]);
    }

    /**
     * @param $target
     * @param array $ids
     * @return array
     */
    private function findByIds($target, array $ids)
    {
        $alias = substr($target, 0, 1);
        $class = 'AppBundle\Entity\Component\\' . ucfirst($target);

        $qb = $this->getQueryBuilder($target, $alias, $class);

        $qb->where($qb->expr()->in(sprintf('%s.id', $alias), $ids));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $id
     * @param $alias
     * @param $class
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder($id, $alias, $class)
    {
        return $this
            ->manager($id)
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($alias)
            ->from($class, $alias)
        ;
    }

    /**
     * @param ProjectInterface $project
     */
    public function reset(ProjectInterface $project)
    {
        $this->autoSave(false);
        $this->resetAreas($project);
        $this->resetStructures($project);
        $this->resetStringBoxes($project);
        $this->resetVarieties($project);
        $this->resetInverters($project);
        $this->resetModules($project);

        $this->save($project, true);
    }

    /**
     * @param ProjectInterface $project
     * @param bool $force
     */
    public function save(ProjectInterface $project, $force = false)
    {
        if(!$this->manager){
            $this->manager = $this->manager('project');
        }

        if($this->autoSave || $force){
            $this->manager->save($project);
        }
    }

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($id)
    {
        return $this->container->get(sprintf('%s_manager', $id));
    }

    /**
     * @param array $data
     * @param $string
     * @return bool
     */
    private function hasData(array $data, $string)
    {
        return array_key_exists($string, $data) && !empty($data[$string]);
    }

    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
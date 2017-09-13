<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\VarietyInterface;
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
     * Cache local services
     * @var array
     */
    private $services = [
        'variety_calculator' => null
    ];

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
        /** @var DefaultsResolver $resolver */
        $resolver = $this->container->get('generator_defaults');

        $resolver->setStrategy(DefaultsResolver::STRATEGY_EXCEPTION);

        $defaults = $resolver->setDefaults($defaults)->resolve();

        return $defaults;
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
        $defaults['inf_power'] = $defaults['power'];

        if('consumption' == $defaults['source']){
            $estimator = $this->container->get('power_estimator');
            $power = $estimator->estimate($defaults['consumption'], $defaults['latitude'], $defaults['longitude']);
            $defaults['power'] = $power;
        }

        // MODULES
        $this->generateModules($this->project);

        /** @var ModuleInterface $module */
        $module = $project->getProjectModules()->first()->getModule();
        $powerModule = $module->getMaxPower() / 1000;
        $roundPower = round($defaults['power'] / $powerModule);
        $multiPower = $roundPower * $powerModule;
        $defaults['power'] = $multiPower;

        CriteriaAggregator::promotional($defaults['is_promotional']);

        $defaults['errors'] = [];
        $this->project->setDefaults($defaults);

        // INVERTERS
        $this->generateInverters($this->project);

        // AREAS
        $this->generateAreas($this->project);

        // GROUPS
        $this->generateGroups($this->project);

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

        // RESOLVE ABB-EXTRA REFERENCE
        $this->handleABBInverters($project);

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
        /** @var Precifier $precifier */
        $precifier = $this->container->get('project_precifier');

        $precifier->priceCost($project);

        $precifier->priceSale($project);

        $this->save($project);

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function priceCost(ProjectInterface $project)
    {
        throw new \BadMethodCallException('This method must be removed');
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateModules(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        $criteria = ['id' => $defaults['module']];

        CriteriaAggregator::finish($criteria);

        $module = $this->manager('module')->findOneBy($criteria);

        if($module instanceof ModuleInterface) {

            $projectModule = new ProjectModule();
            $projectModule
                ->setModule($module)
                ->setProject($this->project);

        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @param MakerInterface $maker
     * @return $this|bool
     */
    public function generateInverters(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        $this->resetInverters($project);

        /** @var \AppBundle\Manager\InverterManager $manager */
        $manager = $this->manager('inverter');

        $loader = new InverterLoader($manager);

        $inverters = $loader->load($defaults);

        if(!count($defaults['errors'])) {

            $powerTransformer = 0;
            foreach ($inverters as $inverter) {

                $quantity = $inverter->quantity;

                if (!$inverter instanceof InverterInterface) {
                    $inverter = $manager->find($inverter->id);
                }

                if (3 == $inverter->getPhases()) {
                    if ($defaults['voltage'] != $inverter->getPhaseVoltage()) {
                        $powerTransformer += $inverter->getNominalPower() * $quantity;
                    }
                }

                for ($i = 0; $i < $quantity; $i++) {

                    $projectInverter = new ProjectInverter();
                    $projectInverter
                        ->setInverter($inverter)
                        ->setQuantity(1);

                    $project->addProjectInverter($projectInverter);
                }
            }

            $project->setDefaults($defaults);

            $this->resolveTransformer($project, $powerTransformer);

            ModuleCombiner::combine($project);

            $this->save($project);

        }else{
            $project->setDefaults($defaults);
        }

        return $this;
    }

    /**
     * @param ProjectInterface $project
     * @return $this
     */
    public function generateAreas(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        if(count($defaults['errors']))
            return $this;

        $this->resetAreas($project);

        $latitude = $defaults['latitude'];
        $inclination = abs($latitude) < 10 ? 10 : (int) abs($latitude);
        $orientation = $latitude < 0 ? 0 : 180;
        $projectModule = $project->getProjectModules()->first();
        $projectInverters = $project->getProjectInverters();

        $totalInvertersPower = 0;
        foreach ($projectInverters as $projectInverter){

            $projectInverter->getProjectAreas()->clear();

            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();
            $mppt = $inverter->getMpptNumber();

            $mppts = explode('.', $projectInverter->getOperation());

            foreach ($mppts as $item) {
                for ($i = 0; $i < $projectInverter->getQuantity(); $i++) {

                    $totalInvertersPower += $projectInverter->getInverter()->getNominalPower();

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
                ->setLoss(15)
                ->setOperation($mppt);
        }

        $this->save($project);

        return $this;
    }

    public function generateGroups(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();
        if(count($defaults['errors']))
            return $this;

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

    /**
     * @param ProjectInverter $projectInverter
     */
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

        $serial = $projectInverter->getSerial();
        $parallel = $projectInverter->getParallel();

        foreach ($mppts as $item) {

            $moduleString = 1 == $parallel ? ceil($serial / count($mppts)) : $serial ;
            $stringNumber = floor($parallel / count($mppts));

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
        $defaults = $project->getDefaults();
        if(count($defaults['errors']))
            return $this;

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
        $defaults = $project->getDefaults();
        if(count($defaults['errors']))
            return $this;

        $this->resetVarieties($project);

        $calculator =  $this->service('variety_calculator');
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
        $defaults = $project->getDefaults();
        if(count($defaults['errors']))
            return $this;

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
            if($projectInverter->getId())
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
            if($projectArea->getId())
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
     * @param ProjectInterface $project
     * @param $power
     */
    public function resolveTransformer(ProjectInterface $project, $power)
    {
        $defaults = $project->getDefaults();

        if(null != $currentTransformer = $project->getTransformer()){
            $project->removeTransformer();
            $this->manager('project_variety')->delete($currentTransformer);
        }

        /** @var \AppBundle\Manager\VarietyManager $manager */
        $manager = $this->manager('variety');
        $loader = new TransformerLoader($manager);

        $transformer = $loader->load($power);

        if($power > 0 && $defaults['use_transformer'] && $transformer instanceof VarietyInterface){
            $project->setTransformer($transformer);
        }

        $this->save($project);
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

            $defaults = $project->getDefaults();
            if(array_key_exists('errors', $defaults) && count($defaults['errors'])) return;

            $this->manager->save($project);
        }
    }

    /**
     * Handle after inverters distribution
     * Exclusive handling for ABB inverters with code:22SMA0200380
     * @param ProjectInterface $project
     */
    private function handleABBInverters(ProjectInterface $project)
    {
        $abbInverters = $project->getProjectInverters()->filter(function(ProjectInverter $projectInverter){
            return '22ABB0500380' === $projectInverter->getInverter()->getCode();
        });

        if(!$abbInverters->isEmpty()){

            /** @var VarietyCalculator $calculator */
            $calculator = $this->service('variety_calculator');
            $count = $abbInverters->count();

            $codes = [
                '22ABB0050380' => $count,
                '22ABB0005380' => $count,
                '22ABB5000380' => $count,
                '25MC4I005' => 1
            ];

            foreach ($codes as $code => $quantity){

                $variety = $calculator->findByCriteria([
                    'type' => 'abb-extra',
                    'code' => $code
                ]);

                if($variety){
                    $calculator->addVariety($project, $variety, $quantity);
                }
            }

            $this->save($project);
        }
    }

    /**
     * @param $id
     * @return object
     */
    private function service($id)
    {
        if(!$this->services[$id]){
            $this->services[$id] = $this->createService($id);
        }

        return $this->services[$id];
    }

    /**
     * @param $id
     * @return object
     */
    private function createService($id)
    {
        switch($id){
            case 'variety_calculator':
                /** @var \AppBundle\Manager\VarietyManager $manager */
                $manager = $this->manager('variety');
                return new VarietyCalculator($manager);
                break;
        }

        return null;
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
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}

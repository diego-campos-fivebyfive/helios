<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectExtra;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ProjectInterface $project
     */
    public function project(ProjectInterface $project)
    {
        $this->project = $project;
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
}
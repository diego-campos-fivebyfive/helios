<?php

namespace AppBundle\Service;

use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Manager\ProjectManager;

class ProjectManipulator
{
    /**
     * @var ProjectManager
     */
    private $manager;

    /**
     * ProjectManipulator constructor.
     * @param ProjectManager $manager
     */
    function __construct(ProjectManager $manager)
    {
        $this->manager = $manager;
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

        $this->manager->save($project);
    }

    /**
     * Generate areas for project distribution
     *
     * @param ProjectInterface $project
     */
    public function generateAreas(ProjectInterface $project)
    {
        $latitude = $project->getLatitude();
        $longitude = $project->getLongitude();
        $inclination = (int) abs($latitude);
        $orientation = $longitude < 0 ? 0 : 180;
        $projectModule = $project->getProjectModules()->first();
        $projectInverters = $project->getProjectInverters();
        $countModules = $projectModule->getQuantity();

        $countMppt = 0;
        foreach ($projectInverters as $projectInverter){
            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();
            $countMppt += $inverter->getMpptNumber();
        }

        foreach ($projectInverters as $projectInverter){
            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();
            //$mppt = $this->getMpptOptions($inverter->getMpptNumber());
            $mppt = $inverter->getMpptNumber();

            $percent = floor(($mppt * 100) / $countMppt) / 100;
            $modulePerString = (int) ceil($countModules * $percent);

            $projectArea = new ProjectArea();
            $projectArea
                ->setProjectInverter($projectInverter)
                ->setProjectModule($projectModule)
                ->setInclination($inclination)
                ->setOrientation($orientation)
                ->setStringNumber(1)
                ->setModuleString($modulePerString)
            ;

            $projectInverter
                ->setLoss(10)
                ->setOperation($mppt);
        }

        $this->manager->save($project);
    }

    private function getMpptOptions($mppt)
    {
        return $mppt;
        /*
        $query = $this->manager->getEntityManager()->createQuery('SELECT m FROM AppBundle\Entity\Project\MpptOperation m WHERE m.mppt = :mppt');
        $query->setParameter('mppt', $mppt);
        */
    }
}
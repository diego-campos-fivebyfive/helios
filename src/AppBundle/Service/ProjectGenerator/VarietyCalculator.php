<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Manager\VarietyManager;

class VarietyCalculator
{
    /**
     * @var VarietyManager
     */
    private $manager;

    public function __construct(VarietyManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ProjectInterface $project
     */
    public function calculate(ProjectInterface $project)
    {
        $moduleConnector = $project->getProjectModules()->first()->getModule()->getConnectionType();

        $connectors = [
            $moduleConnector => 0
        ];
        foreach ($project->getProjectInverters() as $projectInverter){

            $connector = $projectInverter->getInverter()->getConnectionType();
            $strings = $projectInverter->getSerial() * $projectInverter->getParallel();

            if('borne' == $connector){
                $connector = 'mc4';
            }

            if(!array_key_exists($connector, $connectors)){
                $connectors[$connector] = 0;
            }

            $connectors[$connector] += $strings;
        }

        foreach($connectors as $subtype => $quantity){

            $connector = $this->manager->findOneBy([
                'type' => 'conector',
                'subtype' => $subtype
            ]);

            if($connector instanceof VarietyInterface){

                $projectVariety = new ProjectVariety();
                $projectVariety
                    ->setProject($project)
                    ->setVariety($connector)
                    ->setQuantity($quantity)
                ;
            }
        }
    }
}
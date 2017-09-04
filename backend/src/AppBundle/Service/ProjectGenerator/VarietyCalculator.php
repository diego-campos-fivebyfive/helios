<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Manager\VarietyManager;

class VarietyCalculator
{
    /**
     * Default cable length
     * @var int
     */
    const CABLE_LENGTH = 30;

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
        $projectAreas = $project->getAreas();

        $moduleConnector = $projectAreas->first()->getProjectModule()->getModule()->getConnectionType();

        $connectors = [
            $moduleConnector => 0
        ];

        $totalStrings = 0;
        /** @var \AppBundle\Entity\Component\ProjectAreaInterface $projectArea */
        foreach ($projectAreas as $projectArea){

            $connector = $projectArea->getProjectInverter()->getInverter()->getConnectionType();
            $strings = $projectArea->getStringNumber();

            if('borne' == $connector){
                $connector = 'mc4';
            }

            if(!array_key_exists($connector, $connectors)){
                $connectors[$connector] = $strings;
            }else{
                $connectors[$connector] += $strings;
            }

            $connectors[$moduleConnector] += $strings;
            $totalStrings += $strings;
        }

        foreach($connectors as $subtype => $quantity){

            $connector = $this->findVariety('conector', $subtype);

            if($connector instanceof VarietyInterface){
                $this->addVariety($project, $connector, $quantity);
            }
        }

        $cables = $totalStrings * self::CABLE_LENGTH;

        $blackCable = $this->findVariety('cabo', 'preto');
        $redCable = $this->findVariety('cabo', 'vermelho');


        if($blackCable instanceof VarietyInterface)
            $this->addVariety($project, $blackCable, $cables);

        if($redCable instanceof VarietyInterface)
            $this->addVariety($project, $redCable, $cables);
    }

    /**
     * @param ProjectInterface $project
     * @param VarietyInterface $variety
     * @param $quantity
     */
    private function addVariety(ProjectInterface $project, VarietyInterface $variety, $quantity)
    {
        $projectVariety = new ProjectVariety();
        $projectVariety
            ->setProject($project)
            ->setVariety($variety)
            ->setQuantity($quantity)
        ;
    }

    /**
     * @param $type
     * @param $subtype
     * @return null|object|VarietyInterface
     */
    private function findVariety($type, $subtype)
    {
        $criteria = ['type' => $type, 'subtype' => $subtype];

        CriteriaAggregator::finish($criteria);

        return $this->manager->findOneBy($criteria);
    }
}
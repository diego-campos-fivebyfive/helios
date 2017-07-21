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

            $connector = $this->findVariety('conector', $subtype);

            if($connector instanceof VarietyInterface){
                $this->addVariety($project, $connector, $quantity);
            }
        }

        $cables = array_sum($connectors) * self::CABLE_LENGTH;

        $blackCable = $this->findVariety('cabo', 'preto');
        $redCable = $this->findVariety('cabo', 'vermelho');

        $this->addVariety($project, $blackCable, $cables);
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
        return $this->manager->findOneBy(['type' => $type, 'subtype' => $subtype]);
    }
}
<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Manager\VarietyManager;

class InverterHandler
{
    const CODE = '22SMA0200380';
    const TYPE = 'abb-extra';

    /**
     * @var VarietyManager
     */
    private $manager;

    /**
     * @param VarietyManager $manager
     */
    function __construct(VarietyManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ProjectInterface $project
     */
    public function addExtra(ProjectInterface $project)
    {
        $count = $project->getProjectInverters()->filter(function(ProjectInverter $projectInverter){
            return self::CODE === $projectInverter->getInverter()->getCode();
        })->count();

        if($count){
            $extras = $this->manager->findBy([
                'type' => self::TYPE
            ]);

            dump($extras); die;
        }
    }
}
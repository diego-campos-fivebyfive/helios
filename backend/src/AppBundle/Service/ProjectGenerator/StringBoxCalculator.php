<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectAreaInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStringBox;

class StringBoxCalculator
{
    /**
     * @var StringBoxLoader
     */
    private $loader;

    /**
     * StringBoxCalculator constructor.
     * @param StringBoxLoader $loader
     */
    function __construct(StringBoxLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param ProjectInterface $project
     */
    public function calculate(ProjectInterface $project, $includeProtected = false)
    {
        $defaults = $project->getDefaults();
        $maker = $defaults['string_box_maker'];
        $collection = [];
        $entities = [];

        /**
         * @var  $key
         * @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter
         */
        foreach ($project->getProjectInverters() as $key => $projectInverter) {

            if (!$projectInverter->getInverter()->hasInProtection() || $includeProtected) {

                $strings = 0;
                foreach ($projectInverter->getProjectAreas() as $projectArea) {
                    $strings += $projectArea->getStringNumber();
                }

                $quantity = 0;
                $mpptNumber = $projectInverter->getProjectAreas()->count();
                $inputs = (int)$strings;

                $stringBoxes = $this->loader->load($inputs, $mpptNumber, $quantity, $maker);

                if(count($stringBoxes)) {

                    /** @var \AppBundle\Entity\Component\StringBoxInterface $stringBox */
                    $stringBox = $stringBoxes[0];

                    if (array_key_exists($stringBox->getId(), $collection)) {
                        $quantity += $collection[$stringBox->getId()];
                    }

                    $collection[$stringBox->getId()] = $quantity;

                    if (!array_key_exists($stringBox->getId(), $entities)) {
                        $entities[$stringBox->getId()] = $stringBox;
                    }
                }
            }
        }

        foreach ($collection as $id => $quantity) {
            $stringBox = $entities[$id];
            $projectStringBox = new ProjectStringBox();
            $projectStringBox
                ->setProject($project)
                ->setQuantity($quantity)
                ->setStringBox($stringBox);
        }
    }
}

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
                foreach($projectInverter->getProjectAreas() as $projectArea){
                    $strings += $projectArea->getStringNumber();
                }

                $quantity = 1;
                $mpptNumber = 1;
                $inputs = $strings;

                $stringBoxes = $this->loader->load($inputs, $mpptNumber, $maker);

                if (!is_null($inputs)) {

                    /** @var \AppBundle\Entity\Component\StringBoxInterface $stringBox */
                    $stringBox = $stringBoxes[0];

                    if (array_key_exists($stringBox->getId(), $collection)) {
                        $quantity += $collection[$stringBox->getId()];
                    }

                    $collection[$stringBox->getId()] = $quantity;

                    if (!array_key_exists($stringBox->getId(), $entities)) {
                        $entities[$stringBox->getId()] = $stringBox;
                    }

                } else {

                    $count = count($stringBoxes);

                    /** @var \AppBundle\Entity\Component\StringBoxInterface $firstStringBox */
                    /** @var \AppBundle\Entity\Component\StringBoxInterface $lastStringBox */
                    for ($j = 1; $j <= 50; $j++) {
                        for ($i = 0; $i < $count; $i++) {

                            $index = $count - 1 - $i;
                            $firstStringBox = $stringBoxes[0];
                            $lastStringBox = $stringBoxes[$index];

                            $total = ($firstStringBox->getInputs() * $j) + $lastStringBox->getInputs();

                            if ($total >= $strings) {

                                $firstQuantity = $j * $quantity;
                                $lastQuantity = 1 * $quantity;
                                $stringEquals = $firstStringBox == $lastStringBox;

                                if (!array_key_exists($firstStringBox->getId(), $collection)) {

                                    $collection[$firstStringBox->getId()] = $firstQuantity;
                                    $entities[$firstStringBox->getId()] = $firstStringBox;

                                } else {

                                    $collection[$firstStringBox->getId()] += $firstQuantity;
                                }

                                if ($stringEquals) {

                                    $collection[$firstStringBox->getId()] += $quantity;

                                } else {

                                    if (!array_key_exists($lastStringBox->getId(), $collection)) {

                                        $collection[$lastStringBox->getId()] = $lastQuantity;
                                        $entities[$lastStringBox->getId()] = $lastStringBox;

                                    }else{

                                        $collection[$lastStringBox->getId()] += $lastQuantity;
                                    }
                                }

                                break 2;
                            }

                        }
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
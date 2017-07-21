<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStringBoxInterface;
use AppBundle\Entity\Component\ProjectStructureInterface;
use AppBundle\Entity\Component\StringBoxInterface;
use FOS\UserBundle\Command\DeactivateUserCommand;

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
    public function calculate(ProjectInterface $project)
    {
        $collection = [];
        foreach ($project->getProjectInverters() as $key => $projectInverter) {
            $quantity = $projectInverter->getQuantity();
            $strings = $projectInverter->getParallel();
            $mpptNumber = 1;

            $stringBoxes = $this->loader->load($strings, $mpptNumber);

            $dataStringBoxes = [];
            foreach ($stringBoxes as $stringBox){

                $projectStringBox = new ProjectStringBox();
                $projectStringBox
                    ->setQuantity(0)
                    ->setStringBox($stringBox)
                ;

                $dataStringBoxes[] = $projectStringBox;
            }

            self::normalize($dataStringBoxes, $quantity, $strings);

            foreach($dataStringBoxes as $projectStringBox){
                $id = $projectStringBox->getStringBox()->getId();
                if(!array_key_exists($id, $collection)){
                    $collection[$id] = $projectStringBox;
                }else{
                    $currentStringBox = $collection[$id];
                    $collection[$id]->setQuantity($currentStringBox->getQuantity() + $projectStringBox->getQuantity());
                }
            }
        }

        foreach ($collection as $id => $projectStringBox){
            if($projectStringBox->getQuantity()){
                $project->addProjectStringBox($projectStringBox);
            }
        }
    }

    /**
     * @param ProjectInterface $project
     * @param $inverters
     * @param $strings
     */
    public static function normalize(&$entities, $inverters, $strings)
    {
        //$projectStringBoxes = $project->getProjectStringBoxes();
        //$count = $projectStringBoxes->count();
        $count = count($entities);

        /**
         * @var ProjectStringBoxInterface $firstStringBox
         * @var ProjectStringBoxInterface $lastStringBox
         */
        for ($j = 1; $j <= 50; $j++) {
            for ($i = 0; $i < $count; $i++) {
                $index = $count - 1 - $i;
                //$firstStringBox = $projectStringBoxes->get(0);
                //$nextStringBox = $projectStringBoxes->get($index);
                //$first = $data[0];
                //$next = $data[$index];

                $firstStringBox = $entities[0];
                $lastStringBox = $entities[$index];

                $result = ($firstStringBox->getStringBox()->getInputs() * $j) + $lastStringBox->getStringBox()->getInputs();

                if ($result >= $strings) {

                    //$firstStringBox->setQuantity($j * $inverters);
                    //$nextStringBox->setQuantity(1 * $inverters);

                    /*$str_box[0]["str_id"] = $str_box_bd[0]["id"];
                    $str_box[0]["qte"] = $j * $inv_qte;
                    $str_box[1]["str_id"] = $str_box_bd[count($str_box_bd) - 1 - $i]["id"];
                    $str_box[1]["qte"] = 1 * $inv_qte;*/

                    //$data[0]['quantity'] = $j * $inverters;
                    //$data[$index]['quantity'] = 1 * $inverters;

                    $firstStringBox->setQuantity($j * $inverters);
                    $lastStringBox->setQuantity($inverters);

                    //dump($data); die;
                    /*if($firstStringBox->getStringBox() == $nextStringBox->getStringBox()){
                        $quantity = $firstStringBox->getQuantity() + $nextStringBox->getQuantity();
                        $firstStringBox->setQuantity($quantity);
                        $projectStringBoxes->removeElement($nextStringBox);
                    }*/

                    break 2;
                }
            }
        }

        /**
         * @var  $key
         * @var ProjectStringBoxInterface $entity
         */
        foreach ($entities as $key => $entity){
            if(!$entity->getQuantity()){
                unset($entities[$key]);
            }
        }

        $entities = array_values($entities);
    }
}
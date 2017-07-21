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

        /*dump($connectors);
        die;

        $mod_bd = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id");
        $mod_con_type = $mod_bd[0]["con_type"];
        //$n_con_mod = 0;
        //echo "$mod_con_type <br>";

        $inv = $inv_out;

        $connectors = array();
        $connectors[0]["con_type"] = $mod_con_type;
        $connectors[0]["qte"] = 0;
        $cont_con = 1;

        for ($i = 0; $i < count($inv); $i++) {
            $inv_id = $inv[$i]["inv_id"];
            $n_string = $inv[$i]["par"] * $inv[$i]["qte"];
            $inv_bd = R::getAll("SELECT * FROM app_component_inverter WHERE id = $inv_id");
            $inv_con_type = $inv_bd[0]["con_type"];
            if ($inv_con_type == "borne"){
                $inv_con_type = "mc4";
            }

            $connectors[0]["qte"] += $n_string; //conectores dos módulos

            $proc = array_search($inv_con_type, array_column($connectors, "con_type"));
            if (!is_numeric($proc)) {
                $connectors[$cont_con]["con_type"] = $inv_con_type;
                $connectors[$cont_con]["qte"] = $n_string;
                $cont_con += 1;
            } else {
                $connectors[$proc]["qte"] += $n_string;
            }
        }

        $con_out = array();
        for ($i = 0; $i < count($connectors); $i++) {
            $con_type = $connectors[$i]["con_type"];

            $con_db = R::getAll("SELECT * FROM misc WHERE tipo = 'conector' AND subtipo = '$con_type'");
            $con_out[$i]["con_id"] = $con_db[0]["id"];
            $con_out[$i]["qte"] = $connectors[$i]["qte"];
        }

        dump($moduleConnector); die;
        */
    }
}
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
                ->setStringNumber($projectInverter->getParallel())
                ->setModuleString($projectInverter->getSerial())
            ;

            $projectInverter
                ->setLoss(10)
                ->setOperation($mppt);
        }

        $this->manager->save($project);
    }

    public static function financial(ProjectInterface $project)
    {
        //$projectFinancial->refresh();

        $finalPrice = $projectFinancial->getFinalPrice();
        $lifeTime = $projectFinancial->getLifetime();
        $energyProduction = $projectFinancial->getEnergyProduction();
        $energyPrice = $projectFinancial->getEnergyPrice();
        $rate = $projectFinancial->getRate();
        $annualCost = $projectFinancial->getAnnualCost();
        $efficiencyLoss = ($projectFinancial->getEfficiencyLoss() / 100) / $lifeTime;

        $cashFlow = [-$finalPrice];
        $accumulatedCash = [-$finalPrice];
        $accumulatedCashDiscounted = 0;

        $y1 = 0;
        $y2 = 0;
        $y1Disc = 0;
        $y2Disc = 0;
        $v1Disc = 0;
        $v2Disc = 0;
        $countDisc = 0;

        for ($i = 0; $i <= $lifeTime; $i++) {

            $enPro = $energyProduction - ($i * ($energyProduction * ($efficiencyLoss)));
            $enPri = $energyPrice * ((1 + ($rate / 100)) ** $i);
            $opCost = $annualCost * ((1 + ($rate / 100)) ** $i);

            if(0 == $i){
                $cashFlow[0] = -$finalPrice;
                $accumulatedCash[0] = -$finalPrice;
                $accumulatedCashDiscounted = -$finalPrice;
            }else{
                $cashFlow[$i] = ($enPro * $enPri) - $opCost;
                $accumulatedCash[$i] = $accumulatedCash[$i-1] + $cashFlow[$i];
                $cashFlowDisc = ($enPro * $energyPrice) - $annualCost;
                $accumulatedCashDiscounted += $cashFlowDisc;
            }

            /*$cashFlow[$i] = ($enPro * $enPri) - $opCost;
            $accumulatedCash[$i] = $accumulatedCash[$i - 1] + $cashFlow[$i];
            $cashFlowDisc = ($enPro * $energyPrice) - $annualCost;
            $accumulatedCashDiscounted += $cashFlowDisc;*/

            if ($accumulatedCash[$i] < 0) {
                $y1 = $i;
                $y2 = $i + 1;
            }

            if ($accumulatedCashDiscounted < 0) {

                $countDisc++;

                $y1Disc = $i;
                $y2Disc = $i + 1;
                $v1Disc = $accumulatedCashDiscounted;
                $v2Disc = $accumulatedCashDiscounted + ((($energyProduction - (($i + 1) * ($energyProduction * ($efficiencyLoss)))) * $energyPrice) - $annualCost);
            }
        }

        $financial = new Financial();

        $projectFinancial->setInternalRateOfReturn(($financial->IRR($cashFlow)) * 100);
        $projectFinancial->setNetPresentValue($financial->NPV($rate / 100, array_slice($cashFlow, 1)));

        if($y2 < count($accumulatedCash)) {

            $payback = $y1 + (((0 - $accumulatedCash[$y1]) / ($accumulatedCash[$y2] - $accumulatedCash[$y1])) * ($y2 - $y1));

            $projectFinancial->setPaybackYears(floor($payback));
            $projectFinancial->setPaybackMonths(floor(($payback - $projectFinancial->getPaybackYears()) * 12));
        }

        if($y2Disc <= $countDisc) {

            $paybackDisc = $y1Disc + (((0 - $v1Disc) / ($v2Disc - $v1Disc)) * ($y2Disc - $y1Disc));

            $projectFinancial->setPaybackYearsDiscounted(floor($paybackDisc));
            $projectFinancial->setPaybackMonthsDiscounted(floor(($paybackDisc - $projectFinancial->getPaybackYearsDiscounted()) * 12));
        }

        $projectFinancial->setAccumulatedCash($accumulatedCash);
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
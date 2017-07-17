<?php

namespace AppBundle\Service\Support\Project;

use AppBundle\Entity\Component\ProjectInterface;

/**
 * Class FinancialAnalyzer
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
abstract class FinancialAnalyzer implements FinancialAnalyzerInterface
{
    public static function analyze(ProjectInterface $project)
    {
        //$projectFinancial->refresh();

        $metadata = $project->getMetadata();

        /*dump($metadata); die;
        dump($project); die;*/

        $finalPrice = $project->getSalePrice();
        $lifeTime = $project->getLifetime();
        $energyProduction = $metadata['total']['kwh_year'];
        $energyPrice = $project->getEnergyPrice();
        $rate = $project->getInflation();
        $annualCost = $project->getAnnualCostOperation();
        $efficiencyLoss = ($project->getEfficiencyLoss() / 100) / $lifeTime;

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

        $project->setInternalRateOfReturn(($financial->IRR($cashFlow)) * 100);
        $project->setNetPresentValue($financial->NPV($rate / 100, array_slice($cashFlow, 1)));

        if($y2 < count($accumulatedCash)) {

            $payback = $y1 + (((0 - $accumulatedCash[$y1]) / ($accumulatedCash[$y2] - $accumulatedCash[$y1])) * ($y2 - $y1));

            $project->setPaybackYears(floor($payback));
            $project->setPaybackMonths(floor(($payback - $project->getPaybackYears()) * 12));
        }

        if($y2Disc <= $countDisc) {

            $paybackDisc = $y1Disc + (((0 - $v1Disc) / ($v2Disc - $v1Disc)) * ($y2Disc - $y1Disc));

            $project->setPaybackYearsDisc(floor($paybackDisc));
            $project->setPaybackMonthsDisc(floor(($paybackDisc - $project->getPaybackYearsDisc()) * 12));
        }

        $project->setAccumulatedCash($accumulatedCash);
    }

    /**
     * @inheritDoc
     */
    public static function legacyAnalyze(ProjectFinancialInterface &$projectFinancial)
    {
        $projectFinancial->refresh();

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
}
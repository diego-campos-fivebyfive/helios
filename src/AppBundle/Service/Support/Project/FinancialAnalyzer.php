<?php

namespace AppBundle\Service\Support\Project;
use AppBundle\Entity\Financial\ProjectFinancialInterface;

/**
 * Class FinancialAnalyzer
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
abstract class FinancialAnalyzer implements FinancialAnalyzerInterface
{
    /**
     * @inheritDoc
     */
    public static function analyze(ProjectFinancialInterface &$projectFinancial)
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

    /**
     * @deprecated
     * @inheritDoc
     */
    public static function legacyAnalyze(ProjectFinancialInterface &$projectFinancial)
    {
        $FinalPrice =$projectFinancial->getFinalPrice();
        $LifeTime =$projectFinancial->getLifetime();
        $AnnualEnergyProduction =$projectFinancial->getEnergyProduction();
        $EnergyPrice =$projectFinancial->getEnergyPrice();
        $Rate =$projectFinancial->getRate();
        $OpCosts =$projectFinancial->getAnnualCost();
        $AnnualEfiLoss = ($projectFinancial->getEfficiencyLoss() / 100) / $LifeTime;

        $CashFlow = array($LifeTime + 1);
        $AccuCash = array($LifeTime + 1);
        /*$CashFlow[0] = -$FinalPrice;
        $AccuCash[0] = -$FinalPrice;*/

        $y1 = 0;
        $y2 = 0;
        $y1_Disc = 0;
        $y2_Disc = 0;
        $v1_Disc = 0;
        $v2_Disc = 0;
        $AccuCash_Disc = 0;

        //var_dump($AnnualEnergyProduction); die;

        for ($i=0; $i <= $LifeTime; $i++ ){
            $EnePro = $AnnualEnergyProduction - ($i * ($AnnualEnergyProduction * ($AnnualEfiLoss)));
            $EnePri = $EnergyPrice * ((1 + ($Rate / 100))**$i);
            $OpCo = $OpCosts * ((1 + ($Rate / 100))**$i);

            if(0 == $i){
                $CashFlow[0] = -$FinalPrice;
                $AccuCash[0] = -$FinalPrice;
                $AccuCash_Disc = -$FinalPrice;
            }else{
                $CashFlow[$i] = ($EnePro * $EnePri) - $OpCo;
                $AccuCash[$i] = $AccuCash[$i-1] + $CashFlow[$i];
                $CashFlow_Disc = ($EnePro * $EnergyPrice) - $OpCosts;
                $AccuCash_Disc += $CashFlow_Disc;
            }

            if ($AccuCash[$i] < 0 ){
                $y1 = $i;
                $y2 = $i + 1;
            }
            if ($AccuCash_Disc < 0){
                $y1_Disc = $i;
                $y2_Disc = $i + 1;
                $v1_Disc = $AccuCash_Disc;
                $v2_Disc = $AccuCash_Disc + ((($AnnualEnergyProduction - (($i+1) * ($AnnualEnergyProduction * ($AnnualEfiLoss)))) * $EnergyPrice) - $OpCosts);
            }
        }

        $f = new Financial();

        $x = ($AccuCash[$y2] - $AccuCash[$y1]);

        $Tir = $f->IRR($CashFlow);
        $Tir *= 100;
        $Vpl = $f->NPV($Rate/100, array_slice($CashFlow, 1));
        $Payback = $y1 + (((0 - $AccuCash[$y1]) / ($AccuCash[$y2] - $AccuCash[$y1])) * ($y2 - $y1));
        $PaybackYears = floor($Payback);
        $PaybackMonths = floor(($Payback - $PaybackYears) * 12);
        $Payback_Disc = $y1_Disc + (((0 - $v1_Disc) / ($v2_Disc - $v1_Disc)) * ($y2_Disc - $y1_Disc));
        $PaybackYears_Disc = floor($Payback_Disc);
        $PaybackMonths_Disc = floor(($Payback_Disc - $PaybackYears_Disc) * 12);

       /*$projectFinancial->internalRateOfReturn = $Tir;
       $projectFinancial->netPresentValue = $Vpl;
       $projectFinancial->paybackYears = $PaybackYears;
       $projectFinancial->paybackMonths = $PaybackMonths;
       $projectFinancial->paybackYearsDiscounted = $PaybackYears_Disc;
       $projectFinancial->paybackMonthsDiscounted = $PaybackMonths_Disc;
       $projectFinancial->accumulatedCash = $AccuCash[$LifeTime];
       */

        return;

    }
}
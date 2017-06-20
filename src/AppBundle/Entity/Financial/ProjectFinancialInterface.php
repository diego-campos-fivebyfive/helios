<?php

namespace AppBundle\Entity\Financial;

use AppBundle\Entity\DocumentInterface;
use AppBundle\Entity\Project\ProjectInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ProjectFinancialInterface
 * @package AppBundle\Entity\Financial
 */
interface ProjectFinancialInterface
{
    /**
     * @param ProjectInterface $project
     * @return ProjectFinancialInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @param $lifetime
     * @return ProjectFinancialInterface
     */
    public function setLifetime($lifetime);

    /**
     * @return int
     */
    public function getLifetime();

    /**
     * @param $rate
     * @return ProjectFinancialInterface
     */
    public function setRate($rate);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @param $efficiencyLoss
     * @return ProjectFinancialInterface
     */
    public function setEfficiencyLoss($efficiencyLoss);

    /**
     * @return float
     */
    public function getEfficiencyLoss();

    /**
     * @param $annualCost
     * @return ProjectFinancialInterface
     */
    public function setAnnualCost($annualCost);

    /**
     * @return int
     */
    public function getAnnualCost();

    /**
     * @param $energyPrice
     * @return ProjectFinancialInterface
     */
    public function setEnergyPrice($energyPrice);

    /**
     * @return float
     */
    public function getEnergyPrice();

    /**
     * @return float
     */
    public function getPriceOriginal();

    /**
     * Original kit price calculated based in kit inverters, modules and items
     *
     * @param $priceEquipments
     * @return ProjectFinancialInterface
     */
    public function setPriceEquipments($priceEquipments);

    /**
     * @return float
     */
    public function getPriceEquipments();

    /**
     * Original price calculated based in kit services
     *
     * @param $priceServices
     * @return ProjectFinancialInterface
     */
    public function setPriceServices($priceServices);

    /**
     * @return float
     */
    public function getPriceServices();

    /**
     * @return float
     */
    public function getFinalPriceServices();

    /**
     * @return float
     */
    public function getFinalPriceEquipments();

    /**
     * @return float
     */
    public function getFinalPrice();
    
    /**
     * @return float
     */
    public function getEnergyProduction();

    /**
     * @param TaxInterface $tax
     * @return ProjectFinancialInterface
     */
    public function addTax(TaxInterface $tax);

    /**
     * @param TaxInterface $tax
     * @return ProjectFinancialInterface
     */
    public function removeTax(TaxInterface $tax);

    /**
     * @return ArrayCollection
     */
    public function getTaxes();

    /**
     * @return bool
     */
    public function hasTaxes();
    
    /**
     * @return array
     */
    public function getServiceTaxes();

    /**
     * @return array
     */
    public function getEquipmentTaxes();

    /**
     * @param $internalRateOfReturn
     * @return ProjectFinancialInterface
     */
    public function setInternalRateOfReturn($internalRateOfReturn);

    /**
     * @return float
     */
    public function getInternalRateOfReturn();

    /**
     * @param $netPresentValue
     * @return ProjectFinancialInterface
     */
    public function setNetPresentValue($netPresentValue);

    /**
     * @return float
     */
    public function getNetPresentValue();

    /**
     * @param $accumulatedCash
     * @return ProjectFinancialInterface
     */
    public function setAccumulatedCash(array $accumulatedCash = []);

    /**
     * @return array
     */
    public function getAccumulatedCash($total = false);

    /**
     * @param $paybackYears
     * @return ProjectFinancialInterface
     */
    public function setPaybackYears($paybackYears);

    /**
     * @return int
     */
    public function getPaybackYears();

    /**
     * @param $paybackMonths
     * @return ProjectFinancialInterface
     */
    public function setPaybackMonths($paybackMonths);

    /**
     * @return int
     */
    public function getPaybackMonths();

    /**
     * @param $paybackYearsDiscounted
     * @return ProjectFinancialInterface
     */
    public function setPaybackYearsDiscounted($paybackYearsDiscounted);

    /**
     * @return int
     */
    public function getPaybackYearsDiscounted();

    /**
     * @param $paybackMonthsDiscounted
     * @return ProjectFinancialInterface
     */
    public function setPaybackMonthsDiscounted($paybackMonthsDiscounted);

    /**
     * @return int
     */
    public function getPaybackMonthsDiscounted();

    /**
     * @return TaxInterface
     */
    public function createTax();

    /**
     * @param $chartData
     * @return ProjectFinancialInterface
     */
    public function setChartData($chartData);

    /**
     * @return string
     */
    public function getChartData();

    /**
     * @param $defaultChartData
     * @return ProjectInterface
     */
    public function setDefaultChartData($defaultChartData);

    /**
     * @return string
     */
    public function getDefaultChartData();

    /**
     * @return mixed
     */
    public function refresh();

    /**
     * @return bool
     */
    public function allowProposal();

    /**
     * @return array
     */
    public function getProposalErrors();

    /**
     * @param DocumentInterface $proposal
     * @return ProjectFinancialInterface
     */
    public function setProposal(DocumentInterface $proposal);

    /**
     * @return DocumentInterface
     */
    public function getProposal();

    /**
     * @return bool
     */
    public function hasProposal();

    /**
     * @return bool
     */
    public function isIssued();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
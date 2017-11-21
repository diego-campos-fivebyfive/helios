<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\CustomerInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\OrderInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Interface ProjectInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectInterface
{
    /**
     * Price strategies
     */
    const PRICE_STRATEGY_ABSOLUTE = 1;
    const PRICE_STRATEGY_SUM      = 2;
    const PRICE_STRATEGY_PERCENT  = 3;

    /**
     * Structure type definitions
     */
    const STRUCTURE_SICES         = 'SICES';
    const STRUCTURE_K2_SYSTEM     = 'K2_SYSTEM';

    /**
     * Type Source
     */
    const SOURCE_PROJECT = 0;
    const SOURCE_ORDER = 1;
    const SOURCE_CATALOG = 2;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $number
     * @return ProjectInterface
     */
    public function setNumber($number);

    /**
     * @return int
     */
    public function getNumber();

    /**
     * @param array $defaults
     * @return ProjectInterface
     */
    public function setDefaults(array $defaults = []);

    /**
     * @return array
     */
    public function getDefaults();

    /**
     * @param array $shippingRules
     * @return ProjectInterface
     */
    public function setShippingRules(array $shippingRules);

    /**
     * @return array
     */
    public function getShippingRules();

    /**
     * @return float
     */
    public function getShipping();

    /**
     * @param $infConsumption
     * @return ProjectInterface
     */
    public function setInfConsumption($infConsumption);

    /**
     * @return float
     */
    public function getInfConsumption();

    /**
     * @param $infPower
     * @return ProjectInterface
     */
    public function setInfPower($infPower);

    /**
     * @return float
     */
    public function getInfPower();

    /**
     * @param $structureType
     * @return ProjectInterface
     */
    public function setStructureType($structureType);

    /**
     * @return string
     */
    public function getStructureType();

    /**
     * @param $identifier
     * @return ProjectInterface
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param $basePrice
     * @return ProjectInterface
     */
    public function setInvoiceBasePrice($basePrice);

    /**
     * @return float
     */
    public function getInvoiceBasePrice();

    /**
     * @param $basePrice
     * @return ProjectInterface
     */
    public function setDeliveryBasePrice($basePrice);

    /**
     * @return float
     */
    public function getDeliveryBasePrice();

    /**
     * @param $strategy
     * @return ProjectInterface
     */
    public function setInvoicePriceStrategy($strategy);

    /**
     * @return int
     */
    public function getInvoicePriceStrategy();

    /**
     * @param $strategy
     * @return ProjectInterface
     */
    public function setDeliveryPriceStrategy($strategy);

    /**
     * @return int
     */
    public function getDeliveryPriceStrategy();

    /**
     * @param $address
     * @return ProjectInterface
     */
    public function setAddress($address);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param float $latitude
     * @return ProjectInterface
     */
    public function setLatitude($latitude);

    /**
     * @return float
     */
    public function getLatitude();

    /**
     * @param float $longitude
     * @return ProjectInterface
     */
    public function setLongitude($longitude);

    /**
     * @return float
     */
    public function getLongitude();

    /**
     * @param $costPrice
     * @return ProjectInterface
     */
    public function setCostPrice($costPrice);

    /**
     * @return float
     */
    public function getCostPrice();

    /**
     * @return float
     */
    public function getSalePrice();

    /**
     * @param $type
     * @param $chart
     * @return ProjectInterface
     */
    public function setChart($type, $chart);

    /**
     * @param $type
     * @return mixed
     */
    public function getChart($type);

    /**
     * @param array $metadata
     * @return ProjectInterface
     */
    public function setMetadata(array $metadata);

    /**
     * @param null $key
     * @return mixed
     */
    public function getMetadata($key = null, $default = null);

    /**
     * @param null $key
     * @return bool
     */
    public function hasMetadata($key = null);

    /**
     * @return float
     */
    public function getAnnualProduction();

    /**
     * @param bool $deep Keys represent months
     * @return array
     */
    public function getMonthlyProduction($deep = false);

    /**
     * @return array
     */
    public function getDistribution();

    /**
     * Count modules referred without areas
     * @return int
     */
    public function countAssociatedModules();

    /**
     * Count inverters referred without areas
     * @return int
     */
    public function countAssociatedInverters();

    /**
     * @return int
     */
    public function countConfiguredModules();

    /**
     * @return float
     */
    public function getArea();

    /**
     * @return float
     */
    public function getPower();

    /**
     * @param $taxPercent
     * @return ProjectInterface
     */
    public function setTaxPercent($taxPercent);

    /**
     * @return float
     */
    public function getTaxPercent();

    /**
     * @return float
     */
    public function getCostPriceModules();

    /**
     * @return float
     */
    public function getCostPriceInverters();

    /**
     * @return float
     */
    public function getCostPriceStringBoxes();

    /**
     * @return float
     */
    public function getCostPriceStructures();

    /**
     * @return float
     */
    public function getCostPriceVarieties();

    /**
     * @return float
     */
    public function getCostPriceComponents();

    /**
     * @return float
     */
    public function getDeliveryPrice();

    /**
     * @return float
     */
    public function getCostPriceTotal();

    /**
     * @return float
     */
    public function getSalePriceModules();

    /**
     * @return float
     */
    public function getSalePriceInverters();

    /**
     * @return float
     */
    public function getSalePriceStringBoxes();

    /**
     * @return float
     */
    public function getSalePriceStructures();

    /**
     * @return float
     */
    public function getSalePriceVarieties();

    /**
     * @return float
     */
    public function getSalePriceExtraProducts();

    /**
     * @return float
     */
    public function getSalePriceExtras();

    /**
     * @return float
     */
    public function getSalePriceComponents();

    /**
     * @return float
     */
    public function getSalePriceEquipments();

    /**
     * @return float
     */
    public function getSalePriceServices();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param null $tag
     * @return array
     */
    public function getChecklist($tag = null);

    /**
     * @return bool
     */
    public function isComputable();

    /**
     * @param $lifetime
     * @return ProjectInterface
     */
    public function setLifetime($lifetime);

    /**
     * @return int
     */
    public function getLifetime();

    /**
     * @param $inflation
     * @return ProjectInterface
     */
    public function setInflation($inflation);

    /**
     * @return float
     */
    public function getInflation();

    /**
     * @param $efficiencyLoss
     * @return ProjectInterface
     */
    public function setEfficiencyLoss($efficiencyLoss);

    /**
     * @return float
     */
    public function getEfficiencyLoss();

    /**
     * @param $annualCostOperation
     * @return ProjectInterface
     */
    public function setAnnualCostOperation($annualCostOperation);

    /**
     * @return float
     */
    public function getAnnualCostOperation();

    /**
     * @param $energyPrice
     * @return ProjectInterface
     */
    public function setEnergyPrice($energyPrice);

    /**
     * @return float
     */
    public function getEnergyPrice();

    /**
     * @param $internalRateOfReturn
     * @return ProjectInterface
     */
    public function setInternalRateOfReturn($internalRateOfReturn);

    /**
     * @return float
     */
    public function getInternalRateOfReturn();

    /**
     * @param $netPresentValue
     * @return ProjectInterface
     */
    public function setNetPresentValue($netPresentValue);

    /**
     * @return float
     */
    public function getNetPresentValue();

    /**
     * @param $accumulatedCash
     * @return ProjectInterface
     */
    public function setAccumulatedCash(array $accumulatedCash = []);

    /**
     * @return array
     */
    public function getAccumulatedCash($total = false);

    /**
     * @param $paybackYears
     * @return ProjectInterface
     */
    public function setPaybackYears($paybackYears);

    /**
     * @return int
     */
    public function getPaybackYears();

    /**
     * @param $paybackMonths
     * @return ProjectInterface
     */
    public function setPaybackMonths($paybackMonths);

    /**
     * @return int
     */
    public function getPaybackMonths();

    /**
     * @param $paybackYearsDisc
     * @return ProjectInterface
     */
    public function setPaybackYearsDisc($paybackYearsDisc);

    /**
     * @return int
     */
    public function getPaybackYearsDisc();

    /**
     * @param $paybackMonthsDisc
     * @return ProjectInterface
     */
    public function setPaybackMonthsDisc($paybackMonthsDisc);

    /**
     * @return int
     */
    public function getPaybackMonthsDisc();

    /**
     * @param $proposal
     * @return ProjectInterface
     */
    public function setProposal($proposal);

    /**
     * @return string
     */
    public function getProposal();

    /**
     * @param MemberInterface $member
     * @return ProjectInterface
     */
    public function setMember(MemberInterface $member);

    /**
     * @return MemberInterface
     */
    public function getMember();

    /**
     * @param CustomerInterface $customer
     * @return ProjectInterface
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @param CategoryInterface $stage
     * @return ProjectInterface
     */
    public function setStage(CategoryInterface $stage);

    /**
     * @return CategoryInterface
     */
    public function getStage();

    /**
     * @param VarietyInterface $transformer
     * @return ProjectInterface
     */
    public function setTransformer(VarietyInterface $transformer);

    /**
     * @return ProjectVarietyInterface
     */
    public function getTransformer();

    /**
     * @return ProjectInterface
     */
    public function removeTransformer();

    /**
     * @return string
     */
    public function getTransformerStatus();

    /**
     * @param ProjectModuleInterface $projectModule
     * @return ProjectInterface
     */
    public function addProjectModule(ProjectModuleInterface $projectModule);

    /**
     * @param ProjectModuleInterface $projectModule
     * @return ProjectInterface
     */
    public function removeProjectModule(ProjectModuleInterface $projectModule);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectModules();

    /**
     * @param ProjectInverterInterface $projectInverter
     * @return ProjectInterface
     */
    public function addProjectInverter(ProjectInverterInterface $projectInverter);

    /**
     * @param ProjectInverterInterface $projectInverter
     * @return ProjectInterface
     */
    public function removeProjectInverter(ProjectInverterInterface $projectInverter);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectInverters();

    /**
     * @return array
     */
    public function groupInverters();

    /**
     * @param ProjectExtraInterface $projectExtra
     * @return ProjectInterface
     */
    public function addProjectExtra(ProjectExtraInterface $projectExtra);

    /**
     * @param ProjectExtraInterface $projectExtra
     * @return ProjectInterface
     */
    public function removeProjectExtra(ProjectExtraInterface $projectExtra);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectExtras();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectExtraProducts();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectExtraServices();

    /**
     * @param ProjectStringBoxInterface $projectStringBox
     * @return ProjectInterface
     */
    public function addProjectStringBox(ProjectStringBoxInterface $projectStringBox);

    /**
     * @param ProjectStringBoxInterface $projectStringBox
     * @return ProjectInterface
     */
    public function removeProjectStringBox(ProjectStringBoxInterface $projectStringBox);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectStringBoxes();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAreas();

    /**
     * @param ProjectStructureInterface $projectStructure
     * @return ProjectInterface
     */
    public function addProjectStructure(ProjectStructureInterface $projectStructure);

    /**
     * @param ProjectStructureInterface $projectStructure
     * @return ProjectInterface
     */
    public function removeProjectStructure(ProjectStructureInterface $projectStructure);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectStructures();

    /**
     * @param ProjectVarietyInterface $projectVariety
     * @return ProjectInterface
     */
    public function addProjectVariety(ProjectVarietyInterface $projectVariety);

    /**
     * @param ProjectVarietyInterface $projectVariety
     * @return ProjectInterface
     */
    public function removeProjectVariety(ProjectVarietyInterface $projectVariety);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectVarieties();

    /**
     * @param ProjectStructureInterface $projectTax
     * @return ProjectInterface
     */
    public function addProjectTax(ProjectTaxInterface $projectTax);

    /**
     * @param ProjectStructureInterface $projectTax
     * @return ProjectInterface
     */
    public function removeProjectTax(ProjectTaxInterface $projectTax);

    /**
     * @param $issuedAt
     * @return ProjectInterface
     */
    public function setIssuedAt($issuedAt);

    /**
     * @return \DateTime
     */
    public function getIssuedAt();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectTaxes();

    /**
     * @return float
     */
    public function getCostPriceExtraServices();

    /**
     * @return float
     */
    public function getCostPriceExtraProducts();

    /**
     * @return float
     */
    public function getCostPriceExtra();

    /**
     * @param $level
     * @return ProjectInterface
     */
    public function setLevel($level);

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @return bool
     */
    public function isClosed();

    /**
     * @return bool
     */
    public function isPromotional();

    /**
     * @param $source
     * @return ProjectInterface
     */
    public function setSource($source);

    /**
     * @return int
     */
    public function getSource();

    /**
     * @return array
     */
    public static function getPriceStrategies();

    /**
     * @return array
     */
    public static function getRoofTypes();

    /**
     * @return array
     */
    public static function getStructureTypes();
}

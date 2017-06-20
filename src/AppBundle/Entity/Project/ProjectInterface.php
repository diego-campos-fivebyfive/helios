<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Component\KitInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ProjectInterface
{
    const ERROR_UNSUPPORTED_CUSTOMER = 'Unsupported [Customer] Definition';
    const ERROR_UNSUPPORTED_MEMBER = 'Unsupported [Member] Definition';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int zerofill
     */
    public function getNumber();

    /**
     * @return string
     */
    public function getToken();

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
     * @param $state
     * @return ProjectInterface
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param $city
     * @return ProjectInterface
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCity();

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
     * @param KitInterface $kit
     * @return KitInterface
     */
    public function setKit(KitInterface $kit);

    /**
     * @return KitInterface
     */
    public function getKit();

    /**
     * @param BusinessInterface $customer
     * @return ProjectInterface
     */
    public function setCustomer(BusinessInterface $customer);

    /**
     * @return BusinessInterface
     */
    public function getCustomer();

    /**
     * @param CategoryInterface $saleStage
     * @return ProjectInterface
     */
    public function setSaleStage(CategoryInterface $saleStage);

    /**
     * @return CategoryInterface
     */
    public function getSaleStage();

    /**
     * @param BusinessInterface $member
     * @return ProjectInterface
     */
    public function setMember(BusinessInterface $member);

    /**
     * @return BusinessInterface
     */
    public function getMember();

    /**
     * @param ProjectInverterInterface $inverter
     * @return ProjectInterface
     */
    public function addInverter(ProjectInverterInterface $inverter);

    /**
     * @param ProjectInverterInterface $inverter
     * @return ProjectInterface
     */
    public function removeInverter(ProjectInverterInterface $inverter);

    /**
     * @return ArrayCollection
     */
    public function getInverters();

    /**
     * @return ArrayCollection
     */
    public function getKitInverters();

    /**
     * @param BudgetSectionInterface $section
     * @return ProjectInterface
     */
    public function addSection(BudgetSectionInterface $section);

    /**
     * @param BudgetSectionInterface $section
     * @return ProjectInterface
     */
    public function removeSection(BudgetSectionInterface $section);

    /**
     * @return ArrayCollection
     */
    public function getSections();

    /**
     * Return sum powers for all areas
     * @return float
     */
    public function getPower();

    /**
     * Price only after financial analysis
     * @return float
     */
    public function getPrice();

    /**
     * @param $calculationChart
     * @return ProjectInterface
     */
    public function setCalculationChart($calculationChart);

    /**
     * @return string
     */
    public function getCalculationChart();

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
     * @param $chartData
     * @return ProjectInterface
     */
    public function setChartData($chartData);

    /**
     * @return string
     */
    public function getChartData();

    /**
     * @return bool
     */
    public function isComputable();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return array
     */
    public function getMetadataOperations();

    /**
     * @param $key
     * @param $metadata
     * @return ProjectInterface
     */
    public function setMetadata($key, $metadata);

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
    public function toArray($legacy = true);

    /**
     * @return ArrayCollection
     */
    public function getConfiguredModules();

    /**
     * @return float
     */
    public function getTotalArea();

    /**
     * @return int
     */
    public function getTotalModules();

    /**
     * @return ArrayCollection
     */
    public function getModules();

    /**
     * @return bool
     */
    public function isAnalysable();

    /**
     * This method return errors at steps between project and financial
     * 
     * @return array
     */
    public function getAnalysisErrors();

    /**
     * @return string
     */
    public function parseFilename();

    /**
     * @return bool
     */
    public function hasProposal();

    /**
     * @return bool
     */
    public function isDone();

    /**
     * @param ProjectFinancialInterface|null $financial
     * @return ProjectInterface
     */
    public function setFinancial(ProjectFinancialInterface $financial = null);

    /**
     * @return ProjectFinancialInterface
     */
    public function getFinancial();

    /**
     * Check if the associated kit corresponds exactly
     * to the configured distribution
     *
     * @param KitInterface|null $previousKit
     * @return bool
     */
    public function assertKitDistribution(KitInterface $previousKit = null);

    /**
     * @return array
     */
    public function getSnapshot();

    /**
     * @see  Used by the price calculator via kit
     * @return float
     */
    public function getFinalCost();

    /**
     * @return float
     */
    public function getFreightPrice();

    /**
     * @return float
     */
    public function getCostOfEquipments();

    /**
     * @return float
     */
    public function getCostTotal();

    /**
     * @see  Used by the price calculator via kit
     * @return float
     */
    public function getTotalPriceServices();

    /**
     * @return float
     */
    public function getTotalPriceComponents();

    /**
     * @return float
     */
    public function getTotalPriceElements();

    /**
     * @return float
     */
    public function getPriceSaleEquipments();

    /**
     * @return float
     */
    public function getPriceSaleServices();

    /**
     * @return float
     */
    public function getPriceSale();

    /**
     * @return ArrayCollection
     */
    public function getElementItems();

    /**
     * @return ArrayCollection
     */
    public function getElementServices();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return null
     */
    public function prePersist();

    /**
     * @return null
     */
    public function preUpdate();
}

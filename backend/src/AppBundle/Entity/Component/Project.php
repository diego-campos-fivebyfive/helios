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
use AppBundle\Entity\Pricing\InsurableTrait;
use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Service\Pricing\InsurableInterface;
use AppBundle\Service\ProjectGenerator\StructureCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Project
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Project implements ProjectInterface, InsurableInterface
{
    use TokenizerTrait;
    use InsurableTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $defaults;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $shippingRules;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $charts;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $invoiceBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $deliveryBasePrice;

    /**
     * @var int
     *
     * @ORM\Column(name="invoice_price_strategy", type="integer")
     */
    private $invoicePriceStrategy;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_price_strategy", type="integer")
     */
    private $deliveryPriceStrategy;

    /**
     * @var string
     ** @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $infConsumption;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $infPower;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $structureType;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $taxPercent;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $costPrice;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $lifetime;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $inflation;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $efficiencyLoss;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $annualCostOperation;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $energyPrice;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $internalRateOfReturn;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $netPresentValue;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $accumulatedCash;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paybackYears;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paybackMonths;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paybackYearsDisc;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paybackMonthsDisc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $proposal;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $level;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issued_at", type="datetime", nullable=true)
     */
    private $issuedAt;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", name="source", nullable=true)
     */
    private $source;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectModule", mappedBy="project", cascade={"persist", "remove"})
     */
    private $projectModules;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectInverter", mappedBy="project", cascade={"persist", "remove"})
     */
    private $projectInverters;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectStructure", mappedBy="project", cascade={"persist", "remove"})
     */
    private $projectStructures;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectExtra", mappedBy="project", indexBy="project", cascade={"persist", "remove"})
     */
    private $projectExtras;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectStringBox", mappedBy="project", cascade={"persist", "remove"})
     */
    private $projectStringBoxes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectVariety", mappedBy="project", cascade={"persist", "remove"})
     */
    private $projectVarieties;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectTax", mappedBy="project", indexBy="project", cascade={"persist", "remove"})
     */
    private $projectTaxes;

    /**
     * @var Customer|MemberInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $member;

    /**
     * @var CustomerInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $customer;

    /**
     * @var \AppBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
     */
    private $stage;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->invoicePriceStrategy = self::PRICE_STRATEGY_ABSOLUTE;
        $this->deliveryPriceStrategy = self::PRICE_STRATEGY_ABSOLUTE;
        $this->projectModules = new ArrayCollection();
        $this->projectInverters = new ArrayCollection();
        $this->projectStructures = new ArrayCollection();
        $this->projectVarieties = new ArrayCollection();
        $this->projectExtras = new ArrayCollection();
        $this->projectStringBoxes = new ArrayCollection();
        $this->projectTaxes = new ArrayCollection();
        $this->invoiceBasePrice = 0;
        $this->deliveryBasePrice = 0;
        $this->taxPercent = 0;
        $this->charts = [];
        $this->defaults = [];
        $this->shippingRules = [];
        $this->metadata = [];
        $this->accumulatedCash = [];
        //REMOVE FIELDS
        $this->infPower = 0;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @inheritDoc
     */
    public function setDefaults(array $defaults = [])
    {
        $defaults['latitude'] = (float) $defaults['latitude'];
        $defaults['longitude'] = (float) $defaults['longitude'];
        $defaults['power'] = (float) $defaults['power'];
        $defaults['consumption'] = (float) $defaults['consumption'];

        $this->latitude = $defaults['latitude'];
        $this->longitude = $defaults['longitude'];

        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaults()
    {
        // TODO: Remove this code when production is stabilized
        if(!array_key_exists('power_transformer', $this->defaults)) {
            $transformer = $this->getTransformer();
            $this->defaults['power_transformer'] = $transformer ? $transformer->getPower() : 0;
        }

        return $this->defaults;
    }

    /**
     * @inheritDoc
     */
    public function setShippingRules(array $shippingRules)
    {
        $this->shippingRules = $shippingRules;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShippingRules()
    {
        return $this->shippingRules;
    }

    /**
     * @inheritDoc
     */
    public function getShipping()
    {
        return  is_array($this->shippingRules) && array_key_exists('shipping', $this->shippingRules)
            ? $this->shippingRules['shipping'] : 0 ;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function setInfConsumption($infConsumption)
    {
        $this->infConsumption = (float) $infConsumption;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInfConsumption()
    {
        return $this->infConsumption;
    }

    /**
     * @inheritDoc
     */
    public function setInfPower($infPower)
    {
        $this->infPower = $infPower;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInfPower()
    {
        return $this->infPower;
    }

    /**
     * @inheritDoc
     */
    public function setStructureType($structureType)
    {
        $this->structureType = $structureType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStructureType()
    {
        return $this->structureType;
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceBasePrice($invoiceBasePrice)
    {
        $this->invoiceBasePrice = $invoiceBasePrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceBasePrice()
    {
        return $this->invoiceBasePrice;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryBasePrice($deliveryBasePrice)
    {
        $this->deliveryBasePrice = $deliveryBasePrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryBasePrice()
    {
        return $this->deliveryBasePrice;
    }

    /**
     * @inheritDoc
     */
    public function setInvoicePriceStrategy($invoicePriceStrategy)
    {
        if(!array_key_exists($invoicePriceStrategy, self::getPriceStrategies()))
            $invoicePriceStrategy = self::PRICE_STRATEGY_ABSOLUTE;

        $this->invoicePriceStrategy = $invoicePriceStrategy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInvoicePriceStrategy()
    {
        return $this->invoicePriceStrategy;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryPriceStrategy($deliveryPriceStrategy)
    {
        if(!array_key_exists($deliveryPriceStrategy, self::getPriceStrategies()))
            $deliveryPriceStrategy = self::PRICE_STRATEGY_ABSOLUTE;

        $this->deliveryPriceStrategy = $deliveryPriceStrategy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryPriceStrategy()
    {
        return $this->deliveryPriceStrategy;
    }

    /**
     * @inheritDoc
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @inheritDoc
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @inheritDoc
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @inheritDoc
     */
    public function setTaxPercent($taxPercent)
    {
        $this->taxPercent = $taxPercent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTaxPercent()
    {
        return $this->taxPercent;
    }

    /**
     * @inheritDoc
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @inheritDoc
     */
    public function setInflation($inflation)
    {
        $this->inflation = $inflation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInflation()
    {
        return $this->inflation;
    }

    /**
     * @inheritDoc
     */
    public function setEfficiencyLoss($efficiencyLoss)
    {
        $this->efficiencyLoss = $efficiencyLoss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEfficiencyLoss()
    {
        return $this->efficiencyLoss;
    }

    /**
     * @inheritDoc
     */
    public function setAnnualCostOperation($annualCostOperation)
    {
        $this->annualCostOperation = $annualCostOperation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAnnualCostOperation()
    {
        return (float) $this->annualCostOperation;
    }

    /**
     * @inheritDoc
     */
    public function setEnergyPrice($energyPrice)
    {
        $this->energyPrice = $energyPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEnergyPrice()
    {
        return (float) $this->energyPrice;
    }

    /**
     * @inheritDoc
     */
    public function setInternalRateOfReturn($internalRateOfReturn)
    {
        $this->internalRateOfReturn = $internalRateOfReturn;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInternalRateOfReturn()
    {
        return (float) $this->internalRateOfReturn;
    }

    /**
     * @inheritDoc
     */
    public function setNetPresentValue($netPresentValue)
    {
        $this->netPresentValue = $netPresentValue;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNetPresentValue()
    {
        return (float) $this->netPresentValue;
    }

    /**
     * @inheritDoc
     */
    public function setAccumulatedCash(array $accumulatedCash = [])
    {
        $this->accumulatedCash = $accumulatedCash;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccumulatedCash($total = false)
    {
        return !$total ? $this->accumulatedCash : $this->accumulatedCash[count($this->accumulatedCash)-1];
    }

    /**
     * @inheritDoc
     */
    public function setPaybackYears($paybackYears)
    {
        $this->paybackYears = $paybackYears;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackYears()
    {
        return $this->paybackYears;
    }

    /**
     * @inheritDoc
     */
    public function setPaybackMonths($paybackMonths)
    {
        $this->paybackMonths = $paybackMonths;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackMonths()
    {
        return $this->paybackMonths;
    }

    /**
     * @inheritDoc
     */
    public function setPaybackYearsDisc($paybackYearsDisc)
    {
        $this->paybackYearsDisc = $paybackYearsDisc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackYearsDisc()
    {
        return $this->paybackYearsDisc;
    }

    /**
     * @inheritDoc
     */
    public function setPaybackMonthsDisc($paybackMonthsDisc)
    {
        $this->paybackMonthsDisc = $paybackMonthsDisc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackMonthsDisc()
    {
        return $this->paybackMonthsDisc;
    }

    /**
     * @inheritDoc
     */
    public function setProposal($proposal)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @inheritDoc
     */
    public function getChecklist($tag = null)
    {
        $errors = [
            'modules' => !$this->getProjectModules()->isEmpty(),
            'inverters' => !$this->getProjectInverters()->isEmpty(),
            'areas' => !$this->getAreas()->isEmpty()
        ];

        /** @var ProjectAreaInterface $projectArea */
        foreach ($this->getAreas() as $projectArea){
            if(!$projectArea->isConfigured()) {
                $errors['areas'] = false;
                break;
            }
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function isComputable()
    {
        $checklist = $this->getChecklist();

        return array_sum($checklist) == count($checklist);
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceModules()
    {
        return $this->calculateCostPrices($this->getProjectModules());
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceInverters()
    {
        return $this->calculateCostPrices($this->getProjectInverters());
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceStringBoxes()
    {
        return $this->calculateCostPrices($this->getProjectStringBoxes());
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceStructures()
    {
        return $this->calculateCostPrices($this->getProjectStructures());
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceVarieties()
    {
        return $this->calculateCostPrices($this->getProjectVarieties());
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceComponents()
    {
        $price =  $this->getCostPriceInverters()
            + $this->getCostPriceModules()
            + $this->getCostPriceStringBoxes()
            + $this->getCostPriceStructures()
            + $this->getCostPriceVarieties();

        if(null != $transformer = $this->getTransformer()){
            $price += $transformer->getTotalCostPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     * @todo fixed value!
     */
    public function getDeliveryPrice()
    {
        return 1000;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceModules()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectModule */
        foreach ($this->projectModules as $projectModule){
            $price += $projectModule->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceInverters()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectInverter */
        foreach ($this->projectInverters as $projectInverter){
            $price += $projectInverter->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceStringBoxes()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectStringBox */
        foreach ($this->projectStringBoxes as $projectStringBox){
            $price += $projectStringBox->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceStructures()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectStructure */
        foreach ($this->projectStructures as $projectStructure){
            $price += $projectStructure->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceVarieties()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectVariety */
        foreach ($this->projectVarieties as $projectVariety){
            $price += $projectVariety->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceExtraProducts()
    {
        $price = 0;
        /** @var ProjectElementInterface $extraProduct */
        foreach ($this->getProjectExtraProducts() as $extraProduct){
            $price += $extraProduct->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceExtras()
    {
        $price = 0;
        /** @var ProjectElementInterface $projectExtra */
        foreach ($this->getProjectExtras() as $projectExtra){
            $price += $projectExtra->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceComponents()
    {
        $price = $this->getSalePriceInverters()
            + $this->getSalePriceModules()
            + $this->getSalePriceStringBoxes()
            + $this->getSalePriceStructures()
            + $this->getSalePriceVarieties();

        if(null != $transformer = $this->getTransformer()){
            $price += $transformer->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceEquipments()
    {
        return $this->getSalePriceComponents() + $this->getSalePriceExtraProducts();
    }

    /**
     * @inheritDoc
     */
    public function getSalePriceServices()
    {
        $price = 0;
        /** @var ProjectExtraInterface $projectItemService */
        foreach ($this->getProjectExtraServices() as $projectItemService){
            $price += $projectItemService->getTotalSalePrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceTotal()
    {
        return $this->getCostPriceComponents() + $this->getDeliveryPrice();
    }

    /**
     * @inheritDoc
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * @inheritDoc
     */
    public function getSalePrice()
    {
        $price = $this->getSalePriceEquipments()
            + $this->getSalePriceServices()
            + $this->getShipping()
            + $this->getInsurance()
        ;

        /** @var ProjectTaxInterface $projectTax */
        foreach ($this->projectTaxes as $projectTax){
            $price += $projectTax->getAmount();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function setChart($type, $chart)
    {
        $this->charts[$type] = $chart;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChart($type)
    {
        return array_key_exists($type, $this->charts) ? $this->charts[$type] : null;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null, $default = null)
    {
        if ($key) {
            return $this->hasMetadata($key) ? $this->metadata[$key] : $default;
        }

        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function hasMetadata($key = null)
    {
        if ($this->metadata) {
            if (!$key) {
                return !empty($this->metadata);
            }
            return array_key_exists($key, $this->metadata);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAnnualProduction()
    {
        $metadata = $this->getMetadata();
        if (array_key_exists('total',$metadata))
            return $metadata['total']['kwh_year'];

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getMonthlyProduction($deep = false)
    {
        $production = [];
        if ($this->hasMetadata('areas')) {
            foreach ($this->getMetadata('areas') as $area) {
                $months = $area['months'];
                foreach ($months as $month => $data) {
                    if (!array_key_exists($month, $production))
                        $production[$month] = 0;

                    $production[$month] += $data['total_month'];
                }
            }
        }

        return $deep ? $production : array_values($production);
    }

    /**
     * @inheritDoc
     */
    public function getDistribution()
    {
        $distribution = [];

        $modulesAvailable = 0;
        $modulesConfigured = 0;
        /** @var ProjectModuleInterface $projectModule */
        foreach($this->projectModules as $projectModule){
            $distributionModule = $projectModule->getDistribution();
            $distribution['modules'][$projectModule->getModule()->getId()] = $distributionModule;

            $modulesAvailable += $distributionModule['available'];
            $modulesConfigured += $distributionModule['configured'];
        }

        $distribution['modules_available'] = $modulesAvailable;
        $distribution['modules_configured'] = $modulesConfigured;

        return $distribution;
    }

    /**
     * @inheritDoc
     */
    public function countAssociatedModules()
    {
        $count = 0;
        foreach ($this->projectModules as $projectModule){
            $count += $projectModule->getQuantity();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function countAssociatedInverters()
    {
        $count = 0;
        foreach($this->projectInverters as $projectInverter){
            $count += $projectInverter->getQuantity();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function countConfiguredModules()
    {
        $count = 0;
        foreach ($this->projectInverters as $projectInverter){
            /** @var ProjectAreaInterface $projectArea */
            foreach ($projectInverter->getProjectAreas() as $projectArea){
                $count += $projectArea->getStringNumber() * $projectArea->getModuleString();
            }
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        $area = 0;
        foreach ($this->projectInverters as $projectInverter) {
            /** @var ProjectAreaInterface $projectArea */
            foreach ($projectInverter->getProjectAreas() as $projectArea) {
                $area += $projectArea->getArea();
            }
        }

        return $area;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $power = 0;
        foreach ($this->projectInverters as $projectInverter) {
            $power += $projectInverter->getPower();
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function setMember(MemberInterface $member)
    {
        $this->member = $member;

        $this->level = $member->getAccount()->getLevel();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @inheritDoc
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @inheritDoc
     */
    public function setStage(CategoryInterface $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @inheritDoc
     */
    public function setTransformer(VarietyInterface $transformer)
    {
        if(VarietyInterface::TYPE_TRANSFORMER == $transformer->getType()){

            $this->removeTransformer();

            $projectVariety = new ProjectVariety();
            $projectVariety
                ->setVariety($transformer)
                ->setQuantity(1);

            $this->addProjectVariety($projectVariety);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTransformer()
    {
        return $this->projectVarieties->filter(function(ProjectVarietyInterface $projectVariety){
            return VarietyInterface::TYPE_TRANSFORMER == $projectVariety->getVariety()->getType();
        })->first();
    }

    /**
     * @inheritDoc
     */
    public function removeTransformer()
    {
        if(null != $transformer = $this->getTransformer()){
            $this->removeProjectVariety($transformer);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTransformerStatus()
    {
        $status = 'ignored';
        if($this->defaults['use_transformer'] && $this->defaults['power_transformer']){
            $status = 'required';
            if($this->getTransformer()) {
                $status = 'resolved';
            }
        }

        return $status;
    }

    /**
     * @inheritDoc
     */
    public function addProjectModule(ProjectModuleInterface $projectModule)
    {
        if(!$this->projectModules->contains($projectModule)){

            $this->projectModules->add($projectModule);

            if(!$projectModule->getProject())
                $projectModule->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectModule(ProjectModuleInterface $projectModule)
    {
        if($this->projectModules->contains($projectModule)){
            $this->projectModules->removeElement($projectModule);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectModules()
    {
        return $this->projectModules;
    }

    /**
     * @inheritDoc
     */
    public function addProjectInverter(ProjectInverterInterface $projectInverter)
    {
        if(!$this->projectInverters->contains($projectInverter)){

            $this->projectInverters->add($projectInverter);

            if(!$projectInverter->getProject())
                $projectInverter->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectInverter(ProjectInverterInterface $projectInverter)
    {
        if($this->projectInverters->contains($projectInverter)){
            $this->projectInverters->removeElement($projectInverter);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectInverters()
    {
        return $this->projectInverters;
    }

    /**
     * @inheritDoc
     */
    public function groupInverters()
    {
        $collection = [];
        foreach ($this->projectInverters as $projectInverter){

            /** @var InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();
            $id = $inverter->getId();

            if(array_key_exists($id, $collection)){
                $collection[$id]['quantity'] += $projectInverter->getQuantity();
            }else{
                $collection[$id] = [
                    'inverter' => $inverter,
                    'projectInverter' => $projectInverter,
                    'quantity' => $projectInverter->getQuantity()
                ];
            }

            // Prevent price overwriting with zero value
            $collection[$id]['unitCostPrice'] = $projectInverter->getUnitCostPrice();
            $collection[$id]['unitSalePrice'] = $projectInverter->getUnitSalePrice();
            $collection[$id]['totalSalePrice'] = $projectInverter->getUnitSalePrice() * $collection[$id]['quantity'];
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function addProjectStructure(ProjectStructureInterface $projectStructure)
    {
        if(!$this->projectStructures->contains($projectStructure)){

            $this->projectStructures->add($projectStructure);

            if(!$projectStructure->getProject())
                $projectStructure->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectStructure(ProjectStructureInterface $projectStructure)
    {
        if($this->projectStructures->contains($projectStructure)){
            $this->projectStructures->removeElement($projectStructure);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectStructures()
    {
        return $this->projectStructures;
    }

    /**
     * @inheritDoc
     */
    public function addProjectVariety(ProjectVarietyInterface $projectVariety)
    {
        if(!$this->projectVarieties->contains($projectVariety)){

            $this->projectVarieties->add($projectVariety);

            if(!$projectVariety->getProject())
                $projectVariety->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectVariety(ProjectVarietyInterface $projectVariety)
    {
        if($this->projectVarieties->contains($projectVariety)){
            $this->projectVarieties->removeElement($projectVariety);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectVarieties()
    {
        return $this->projectVarieties->filter(function (ProjectVarietyInterface $projectVariety){
            return VarietyInterface::TYPE_TRANSFORMER != $projectVariety->getVariety()->getType();
        });
    }

    /**
     * @inheritDoc
     */
    public function addProjectExtra(ProjectExtraInterface $projectExtra)
    {
        if(!$this->projectExtras->contains($projectExtra)){

            $this->projectExtras->add($projectExtra);

            if(!$projectExtra->getProject())
                $projectExtra->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectExtra(ProjectExtraInterface $projectExtra)
    {
        if($this->projectExtras->contains($projectExtra)){
            $this->projectExtras->removeElement($projectExtra);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectExtras()
    {
        return $this->projectExtras;
    }



    /**
     * @inheritDoc
     */
    public function getProjectExtraProducts()
    {
        return $this->projectExtras->filter(function(ProjectExtraInterface $projectExtra){
            return $projectExtra->isProduct();
        });
    }

    /**
     * @inheritDoc
     */
    public function getProjectExtraServices()
    {
        return $this->projectExtras->filter(function(ProjectExtraInterface $projectExtra){
            return $projectExtra->isService();
        });
    }

    /**
     * @inheritDoc
     */
    public function addProjectStringBox(ProjectStringBoxInterface $projectStringBox)
    {
        if(!$this->projectStringBoxes->contains($projectStringBox)){

            $this->projectStringBoxes->add($projectStringBox);

            if(!$projectStringBox->getProject())
                $projectStringBox->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectStringBox(ProjectStringBoxInterface $projectStringBox)
    {
        if($this->projectStringBoxes->contains($projectStringBox)){
            $this->projectStringBoxes->removeElement($projectStringBox);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectStringBoxes()
    {
        return $this->projectStringBoxes;
    }


    /**
     * @inheritDoc
     */
    public function addProjectTax(ProjectTaxInterface $projectTax)
    {
        if(!$this->projectTaxes->contains($projectTax)){

            $this->projectTaxes->add($projectTax);

            if(!$projectTax->getProject())
                $projectTax->setProject($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectTax(ProjectTaxInterface $projectTax)
    {
        if($this->projectTaxes->contains($projectTax)){
            $this->projectTaxes->removeElement($projectTax);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectTaxes()
    {
        return $this->projectTaxes;
    }

    /**
     * @inheritDoc
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceExtraServices()
    {
        $price = 0;
        foreach ($this->getProjectExtraServices() as $projectExtraService){
            $price += $projectExtraService->getTotalCostPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceExtraProducts()
    {
        $price = 0;
        foreach ($this->getProjectExtraProducts() as $projectExtraProduct){
            $price += $projectExtraProduct->getTotalCostPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getCostPriceExtra()
    {
        return $this->getCostPriceExtraServices() + $this->getCostPriceExtraProducts();
    }

    /**
     * @inheritDoc
     */
    public function getAreas()
    {
        $areas = new ArrayCollection();

        foreach ($this->projectInverters as $projectInverter){
            foreach ($projectInverter->getProjectAreas() as $projectArea){
                $areas->add($projectArea);
            }
        }

        return $areas;
    }

    /**
     * @inheritDoc
     */
    public function getInsuranceQuota()
    {
        return $this->getCostPriceComponents();
    }

    /**
     * @inheritDoc
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function isClosed()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isPromotional()
    {
        return array_key_exists('is_promotional', $this->defaults) && $this->defaults['is_promotional'];
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->source;
    }


    /**
     * @inheritDoc
     */
    public static function getPriceStrategies()
    {
        return [
            self::PRICE_STRATEGY_ABSOLUTE => 'absolute',
            self::PRICE_STRATEGY_SUM => 'sum',
            self::PRICE_STRATEGY_PERCENT => 'percent'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRoofTypes()
    {
        return array_combine(StructureCalculator::getRoofTypes(), StructureCalculator::getRoofTypes());
    }

    /**
     * @inheritDoc
     */
    public static function getStructureTypes()
    {
        $types = [self::STRUCTURE_SICES, self::STRUCTURE_K2_SYSTEM];

        return array_combine($types, $types);
    }

    /**
     * @param $components
     * @return float|int
     */
    private function calculateCostPrices($components)
    {
        $price = 0;
        /** @var ProjectElementInterface $component */
        foreach ($components as $component){
            $price += $component->getTotalCostPrice();
        }

        return $price;
    }
}

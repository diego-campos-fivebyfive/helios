<?php

namespace AppBundle\Entity\Financial;

use AppBundle\Entity\DocumentInterface;
use AppBundle\Entity\Project\ProjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ProjectFinancial
 *
 * @ORM\Table(name="app_financial")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectFinancial implements ProjectFinancialInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="lifetime", type="smallint", nullable=true)
     */
    private $lifetime;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float", nullable=true)
     */
    private $rate;

    /**
     * @var float
     *
     * @ORM\Column(name="efficiency_loss", type="float", nullable=true)
     */
    private $efficiencyLoss;

    /**
     * @var float
     *
     * @ORM\Column(name="annual_cost", type="float", nullable=true)
     */
    private $annualCost;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_price", type="float", nullable=true)
     */
    private $energyPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_production", type="float")
     */
    private $energyProduction;

    /**
     * @var float
     *
     * @ORM\Column(name="price_equipments", type="float", nullable=true)
     */
    private $priceEquipments;

    /**
     * @var float
     *
     * @ORM\Column(name="price_services", type="float", nullable=true)
     */
    private $priceServices;

    /**
     * @var float
     *
     * @ORM\Column(name="internal_rate_of_return", type="float", nullable=true)
     */
    private $internalRateOfReturn;

    /**
     * @var float
     *
     * @ORM\Column(name="net_present_value", type="float", nullable=true)
     */
    private $netPresentValue;

    /**
     * @var json
     *
     * @ORM\Column(name="accumulated_cash", type="json", nullable=true)
     */
    private $accumulatedCash;

    /**
     * @var integer
     *
     * @ORM\Column(name="payback_years", type="smallint", nullable=true)
     */
    private $paybackYears;

    /**
     * @var integer
     *
     * @ORM\Column(name="payback_months", type="smallint", nullable=true)
     */
    private $paybackMonths;

    /**
     * @var integer
     *
     * @ORM\Column(name="payback_years_discounted", type="smallint", nullable=true)
     */
    private $paybackYearsDiscounted;

    /**
     * @var integer
     *
     * @ORM\Column(name="payback_months_discounted", type="smallint", nullable=true)
     */
    private $paybackMonthsDiscounted;

    /**
     * @var string
     *
     * @ORM\Column(name="default_chart_data", type="text", nullable=true)
     */
    private $defaultChartData;

    /**
     * @var string
     *
     * @ORM\Column(name="chart_data", type="text", nullable=true)
     */
    private $chartData;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Project\Project
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Project\Project", inversedBy="financial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id", unique=true, nullable=false)
     * })
     */
    private $project;

    /**
     * @var \AppBundle\Entity\Document
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Document")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proposal_id", referencedColumnName="id", unique=true)
     * })
     */
    private $proposal;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Financial\Tax", mappedBy="financial", cascade={"persist","remove"})
     */
    private $taxes;

    /**
     * ProjectFinancial constructor.
     */
    function __construct()
    {
        $this->taxes = new ArrayCollection();
        $this->accumulatedCash = [];
    }

    /**
     * @inheritDoc
     */
    function __clone()
    {
        $this->id = null;
        $this->token = null;
        $this->project = null;
        $this->proposal = null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;
        $this->energyProduction = $project->getAnnualProduction();

        // TODO: Changed! Check if is a correct implementation
        $this->priceServices = $project->getKit()->getPriceSaleServices();
        $this->priceEquipments = $project->getKit()->getPriceSaleEquipments();
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
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
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRate()
    {
        return $this->rate;
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
    public function setAnnualCost($annualCost)
    {
        $this->annualCost = $annualCost;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAnnualCost()
    {
        return round($this->annualCost, 2);
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
        return round($this->energyPrice, 2);
    }

    /**
     * @inheritDoc
     */
    public function setPriceEquipments($priceEquipments)
    {
        $this->priceEquipments = $priceEquipments;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriceEquipments()
    {
        if(null != $kit = $this->project->getKit()) {
            if($kit->isPriceComputable()){
                $this->priceEquipments = $this->project->getPriceSaleEquipments();
            }else{
                $this->priceEquipments = $kit->getPriceSaleEquipments();
            }
        }

        return $this->priceEquipments;
    }

    /**
     * @inheritDoc
     */
    public function setPriceServices($priceServices)
    {
        $this->priceServices = $priceServices;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriceServices()
    {
        if(null != $kit = $this->project->getKit()) {
            //$this->priceServices = $kit->getPriceSaleServices();
            $this->priceServices = $this->project->getPriceSaleServices();
        }

        return $this->priceServices;
    }

    /**
     * @inheritDoc
     */
    public function getPriceOriginal()
    {
        return $this->getPriceEquipments() + $this->getPriceServices();
    }

    /**
     * @inheritDoc
     */
    public function getFinalPriceEquipments()
    {
        //return $this->computeValueTaxes($this->getPriceEquipments(), $this->getEquipmentTaxes());
    }

    /**
     * @inheritDoc
     */
    public function getFinalPriceServices()
    {
        //return $this->computeValueTaxes($this->getPriceServices(), $this->getServiceTaxes());
    }

    /**
     * @inheritDoc
     */
    public function getFinalPrice()
    {
        $price = $this->getPriceOriginal();

        if(!empty($this->taxes)) {
            foreach ($this->taxes as $tax) {
                $price += $tax->getAmount();
            }
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getEnergyProduction()
    {
        return $this->energyProduction;
    }

    public function refresh()
    {
        $this->setProject($this->getProject());
    }

    /**
     * @inheritDoc
     */
    public function addTax(TaxInterface $tax)
    {
        if(!$this->taxes->contains($tax))
            $this->taxes->add($tax);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeTax(TaxInterface $tax)
    {
        if($this->taxes->contains($tax))
            $this->taxes->removeElement($tax);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @inheritDoc
     */
    public function hasTaxes()
    {
        return $this->taxes->count();
    }

    /**
     * @inheritDoc
     */
    public function getServiceTaxes()
    {
    }

    /**
     * @inheritDoc
     */
    public function getEquipmentTaxes()
    {
    }

    /**
     * @inheritDoc
     */
    public function getGeneralTaxes()
    {

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
        return round($this->internalRateOfReturn, 2);
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
        return $this->netPresentValue;
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
    public function setPaybackYearsDiscounted($paybackYearsDiscounted)
    {
        $this->paybackYearsDiscounted = $paybackYearsDiscounted;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackYearsDiscounted()
    {
        return $this->paybackYearsDiscounted;
    }

    /**
     * @inheritDoc
     */
    public function setPaybackMonthsDiscounted($paybackMonthsDiscounted)
    {
        $this->paybackMonthsDiscounted = $paybackMonthsDiscounted;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaybackMonthsDiscounted()
    {
        return $this->paybackMonthsDiscounted;
    }

    public function setChartData($chartData)
    {
        $this->chartData = $chartData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChartData()
    {
        return $this->chartData;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultChartData($defaultChartData)
    {
        $this->defaultChartData = $defaultChartData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultChartData()
    {
        return $this->defaultChartData;
    }

    /**
     * @inheritDoc
     */
    public function createTax()
    {
        return new Tax($this);
    }

    /**
     * @inheritDoc
     */
    public function allowProposal()
    {
        return empty($this->getProposalErrors());
    }

    /**
     * @inheritDoc
     */
    public function getProposalErrors()
    {
        $errors = [];
        if(!$this->project->isAnalysable()){
            $errors = $this->project->getAnalysisErrors();
        }

        if(empty($this->accumulatedCash) /*|| !$this->chartData*/){
            $errors[] = 'analysis_not_calculated';
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function setProposal(DocumentInterface $proposal)
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
    public function hasProposal()
    {
        return $this->proposal instanceof DocumentInterface;
    }

    /**
     * @inheritDoc
     */
    public function isIssued()
    {
        if($this->project){
            return $this->project->isDone();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = [];
        foreach(get_object_vars($this) as $var => $value){
            if(!is_object($value)){
                $data[$var] = $value;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->generateToken();
    }
}


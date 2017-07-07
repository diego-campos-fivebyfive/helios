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

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerInterface;
use AppBundle\Entity\MemberInterface;
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
class Project implements ProjectInterface
{
    use TokenizerTrait;
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
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255, nullable=true)
     */
    private $identifier;

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
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectModule", mappedBy="project", cascade={"persist"})
     */
    private $projectModules;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectInverter", mappedBy="project", cascade={"persist"})
     */
    private $projectInverters;

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
     * @inheritDoc
     */
    public function __construct()
    {
        $this->invoicePriceStrategy  = self::PRICE_STRATEGY_ABSOLUTE;
        $this->deliveryPriceStrategy = self::PRICE_STRATEGY_ABSOLUTE;
        $this->invoiceBasePrice      = 0;
        $this->deliveryBasePrice     = 0;
        $this->projectModules        = new ArrayCollection();
        $this->projectInverters      = new ArrayCollection();
        $this->metadata              = [];
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
    public function getCostPrice()
    {
        // TODO: Implement getCostPrice() method.
    }

    /**
     * @inheritDoc
     */
    public function getSalePrice()
    {
        // TODO: Implement getSalePrice() method.
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
        if (null != $kwhYear = $this->getMetadata('kwh_year'))
            return $kwhYear;

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
    public static function getPriceStrategies()
    {
        return [
            self::PRICE_STRATEGY_ABSOLUTE => 'absolute',
            self::PRICE_STRATEGY_SUM => 'sum',
            self::PRICE_STRATEGY_PERCENT => 'percent'
        ];
    }
}
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

use AppBundle\Entity\CustomerInterface;
use AppBundle\Entity\MemberInterface;
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
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return int
     */
    public function getNumber();

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
     * @return float
     */
    public function getCostPrice();

    /**
     * @return float
     */
    public function getSalePrice();

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
     * @param ProjectExtraInterface $projectExtra
     * @return ProjectInterface
     */
    public function addProjectExtra(ProjectExtraInterface $projectExtra);

    /**
     * @param ProjectExtraInterface $projectItem
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
    public function getProjectExtrasProducts();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjectExtrasServices();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAreas();

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
     * @return array
     */
    public static function getPriceStrategies();
}
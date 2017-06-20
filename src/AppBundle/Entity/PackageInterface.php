<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface PackageInterface
{
    const DISABLED = 0;
    const ENABLED = 1;

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * @param $trail
     * @return PackageInterface
     */
    public function setTrail($trail);

    /**
     * @param $maxMembers
     * @return PackageInterface
     */
    public function setMaxMembers($maxMembers);

    /**
     * @return int
     */
    public function getMaxMembers();

    /**
     * @return int
     */
    public function getTrail();

    /**
     * Set version
     *
     * @param float $version
     *
     * @return Package
     */
    public function setVersion($version);

    /**
     * Get version
     *
     * @return float
     */
    public function getVersion();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Package
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Package
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Package
     */
    public function setPrice($price);

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice();

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Package
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get status
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * @param $default
     * @return PackageInterface
     */
    public function setDefault($default);

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * Get Accounts
     *
     * @return ArrayCollection
     */
    function getAccounts();

    /**
     * Set Accounts
     *
     * @param ArrayCollection $accounts
     */
    function setAccounts($accounts);
}

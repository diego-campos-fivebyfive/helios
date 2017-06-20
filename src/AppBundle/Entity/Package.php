<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Package
 *
 * @ORM\Table(name="app_package")
 * @ORM\Entity
 */
class Package implements PackageInterface
{
    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float")
     */
    private $version = '1.0';

    /**
     * @var integer
     *
     * @ORM\Column(name="trail", type="smallint", length=3)
     */
    private $trail;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_members", type="smallint", length=3)
     */
    private $maxMembers;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $default;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="package")
     */
    private $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->default = false;
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
    public function setTrail($trail)
    {
        if ((int)$trail < 1)
            $trail = 1;

        $this->trail = $trail;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTrail()
    {
        return $this->trail;
    }

    /**
     * @inheritDoc
     */
    public function setMaxMembers($maxMembers)
    {
        if ((int)$maxMembers < 1)
            $maxMembers = 1;

        $this->maxMembers = $maxMembers;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxMembers()
    {
        return $this->maxMembers;
    }

    /**
     * @inheritDoc
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->status == self::ENABLED;
    }

    /**
     * @inheritDoc
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     */
    function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * @inheritDoc
     */
    function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }
}
<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Maker
 *
 * @ORM\Table(name="app_component_maker")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Maker implements MakerInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=20)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Component\Module", mappedBy="maker")
     */
    private $modules;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Component\Inverter", mappedBy="maker")
     */
    private $inverters;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * })
     */
    private $account;

    function __construct()
    {
        $this->enabled = true;
        $this->inverters = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Maker
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Maker
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setAccount(BusinessInterface $account = null)
    {
        $this->account = $account;

        return $this;
    }

    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function isMakerInverter()
    {
        return $this->context === self::CONTEXT_INVERTER;
        //|| $this->context === self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     */
    public function isMakerModule()
    {
        return $this->context === self::CONTEXT_MODULE;
        //|| $this->context === self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     * @deprecated context ALL is removed
     */
    public function isMakerAll()
    {
        throw new \BadMethodCallException('This method is disabled');
        //return $this->context == self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     */
    public function addModule(ModuleInterface $module)
    {
        if(!$this->modules->contains($module)){
            $this->modules->add($module);
            $module->setMaker($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeModule(ModuleInterface $module)
    {
        if($this->modules->contains($module)){
            $this->modules->removeElement($module);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @inheritDoc
     */
    public function addInverter(InverterInterface $inverter)
    {
        if(!$this->inverters->contains($inverter)){
            $this->inverters->add($inverter);
            $inverter->setMaker($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeInverter(InverterInterface $inverter)
    {
        if($this->inverters->contains($inverter)){
            $this->inverters->removeElement($inverter);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverters()
    {
        return $this->inverters;
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public static function getContextList()
    {
        return [
            self::CONTEXT_MODULE => self::CONTEXT_MODULE,
            self::CONTEXT_INVERTER => self::CONTEXT_INVERTER
            //self::CONTEXT_ALL => self::CONTEXT_ALL
        ];
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * @inheritDoc
     */
    public static function unsupportedMakerContextException()
    {
        throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_CONTEXT);
    }
}


<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use AppBundle\Model\Snapshot;

/**
 * Inverter
 *
 * @ORM\Table(name="app_component_inverter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Inverter implements InverterInterface
{
    use TokenizerTrait;
    use ComponentTrait;
    use Snapshot;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="max_dc_power", type="float", nullable=true)
     */
    private $maxDcPower;

    /**
     * @var float
     *
     * @ORM\Column(name="max_dc_voltage", type="float", nullable=true)
     */
    private $maxDcVoltage;

    /**
     * @var float
     *
     * @ORM\Column(name="nominal_power", type="float", nullable=true)
     */
    private $nominalPower;

    /**
     * @var float
     *
     * @ORM\Column(name="mppt_max_dc_current", type="float", nullable=true)
     */
    private $mpptMaxDcCurrent;

    /**
     * @var float
     *
     * @ORM\Column(name="max_efficiency", type="float", nullable=false)
     */
    private $maxEfficiency;

    /**
     * @var float
     *
     * @ORM\Column(name="mppt_max", type="float", nullable=true)
     */
    private $mpptMax;

    /**
     * @var float
     *
     * @ORM\Column(name="mppt_min", type="float", nullable=true)
     */
    private $mpptMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="mppt_number", type="smallint", nullable=true)
     */
    private $mpptNumber;

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
     * @var MakerInterface
     *
     * @ORM\ManyToOne(targetEntity="Maker", inversedBy="inverters")
     * @ORM\JoinColumn(name="maker")
     */
    protected $maker;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childrens = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getModel();
    }

    /**
     * @inheritDoc
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    public function setMaxDcPower($maxDcPower)
    {
        $this->maxDcPower = $maxDcPower;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxDcPower()
    {
        return $this->maxDcPower;
    }

    /**
     * @inheritDoc
     */
    public function setMaxDcVoltage($maxDcVoltage)
    {
        $this->maxDcVoltage = $maxDcVoltage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxDcVoltage()
    {
        return $this->maxDcVoltage;
    }

    /**
     * @inheritDoc
     */
    public function setMpptMaxDcCurrent($mpptMaxDcCurrent)
    {
        $this->mpptMaxDcCurrent = $mpptMaxDcCurrent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptMaxDcCurrent()
    {
        return $this->mpptMaxDcCurrent;
    }

    /**
     * @inheritDoc
     */
    public function setMaxEfficiency($maxEfficiency)
    {
        $this->maxEfficiency = $this->viewMode ? $maxEfficiency / 100 : $maxEfficiency;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxEfficiency()
    {
        return $this->viewMode ? $this->maxEfficiency * 100 : $this->maxEfficiency;
    }

    /**
     * @inheritDoc
     */
    public function setNominalPower($nominalPower)
    {
        $this->nominalPower = $nominalPower;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNominalPower()
    {
        return $this->nominalPower;
    }

    /**
     * @inheritDoc
     */
    public function setMpptMax($mpptMax)
    {
        $this->mpptMax = $mpptMax;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptMax()
    {
        return $this->mpptMax;
    }

    /**
     * @inheritDoc
     */
    public function setMpptMin($mpptMin)
    {
        $this->mpptMin = $mpptMin;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptMin()
    {
        return $this->mpptMin;
    }

    /**
     * @inheritDoc
     */
    public function setMpptNumber($mpptNumber)
    {
        $this->mpptNumber = $mpptNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptNumber()
    {
        return $this->mpptNumber;
    }

    /**
     * @inheritDoc
     * @deprecated
     * @see $mpptMaxDcCurrent property
     */
    public function setMaxDcCurrentMppt($maxDcCurrentMppt)
    {
        return $this->setMpptMaxDcCurrent($maxDcCurrentMppt);
    }

    /**
     * @inheritDoc
     */
    public function getMaxDcCurrentMppt()
    {
        return $this->getMpptMaxDcCurrent();
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();

        return $this;
    }
}


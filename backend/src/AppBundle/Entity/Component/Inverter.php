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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Inverter
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_component_inverter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Inverter implements InverterInterface, ComponentInterface
{
    use ComponentTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $model;

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
     * @ORM\Column(name="max_efficiency", type="float", nullable=true)
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
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $mpptConnections;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $connectionType;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mpptParallel;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inProtection;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $phases;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $phaseVoltage;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $currentPrice;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $compatibility;

    /**
     * This property is used by management only
     * @var bool
     */
    public $viewMode = false;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
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
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
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
    public function setDescription($description)
    {
        $this->model = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
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
     */
    public function setMpptConnections($mpptConnections)
    {
        $this->mpptConnections = $mpptConnections;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptConnections()
    {
        return $this->mpptConnections;
    }

    /**
     * @inheritDoc
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @inheritDoc
     */
    public function setMpptParallel($mpptParallel)
    {
        $this->mpptParallel = $mpptParallel;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMpptParallel()
    {
        return $this->mpptParallel;
    }

    /**
     * @inheritDoc
     */
    public function setInProtection($inProtection)
    {
        $this->inProtection = $inProtection;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasInProtection()
    {
        return $this->inProtection;
    }

    /**
     * @inheritDoc
     */
    public function setPhases($phases)
    {
        $this->phases = $phases;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * @inheritDoc
     */
    public function setPhaseVoltage($phaseVoltage)
    {
        $this->phaseVoltage = $phaseVoltage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPhaseVoltage()
    {
        return $this->phaseVoltage;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentPrice($currentPrice)
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPrice()
    {
        return (float) $this->currentPrice;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setCompatibility($compatibility)
    {
        $this->compatibility = $compatibility;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCompatibility()
    {
        return $this->compatibility;
    }
}

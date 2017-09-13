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
 * Module
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_component_module")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Module implements ModuleInterface
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
     * @ORM\Column(name="code", type="string", nullable=true)
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
     * @var integer
     *
     * @ORM\Column(name="cell_number", type="integer", nullable=true)
     */
    private $cellNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="max_power", type="float", nullable=true)
     */
    private $maxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="voltage_max_power", type="float", nullable=true)
     */
    private $voltageMaxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="current_max_power", type="float", nullable=true)
     */
    private $currentMaxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="open_circuit_voltage", type="float", nullable=true)
     */
    private $openCircuitVoltage;

    /**
     * @var float
     *
     * @ORM\Column(name="short_circuit_current", type="float", nullable=true)
     */
    private $shortCircuitCurrent;

    /**
     * @var float
     *
     * @ORM\Column(name="efficiency", type="float", nullable=true)
     */
    private $efficiency;

    /**
     * @var integer
     *
     * @ORM\Column(name="temperature_operation", type="smallint", nullable=true)
     */
    private $temperatureOperation;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_max_power", type="float", nullable=true)
     */
    private $tempCoefficientMaxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_voc", type="float", nullable=true)
     */
    private $tempCoefficientVoc;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_isc", type="float", nullable=true)
     */
    private $tempCoefficientIsc;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="float", nullable=true)
     */
    private $length;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", nullable=true)
     */
    private $width;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="cell_type", type="string", length=100, nullable=true)
     */
    private $cellType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $connectionType;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $currentPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available", type="boolean", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $available;

    /**
     * This property is used by management only
     * @var bool
     */
    public $viewMode = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->model;
    }

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->status = self::DISABLE;
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
    public function setMaxPower($maxPower)
    {
        $this->maxPower = $maxPower;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxPower()
    {
        return $this->maxPower;
    }

    /**
     * @inheritDoc
     */
    public function setVoltageMaxPower($voltageMaxPower)
    {
        $this->voltageMaxPower = $voltageMaxPower;

        return $this;
    }

    /**
     * Get voltageMaxPower
     *
     * @return float
     */
    public function getVoltageMaxPower()
    {
        return $this->voltageMaxPower;
    }

    /**
     * Set currentMaxPower
     *
     * @param float $currentMaxPower
     * @return Module
     */
    public function setCurrentMaxPower($currentMaxPower)
    {
        $this->currentMaxPower = $currentMaxPower;

        return $this;
    }

    /**
     * Get currentMaxPower
     *
     * @return float
     */
    public function getCurrentMaxPower()
    {
        return $this->currentMaxPower;
    }

    /**
     * Set openCircuitVoltage
     *
     * @param float $openCircuitVoltage
     * @return Module
     */
    public function setOpenCircuitVoltage($openCircuitVoltage)
    {
        $this->openCircuitVoltage = $openCircuitVoltage;

        return $this;
    }

    /**
     * Get openCircuitVoltage
     *
     * @return float
     */
    public function getOpenCircuitVoltage()
    {
        return $this->openCircuitVoltage;
    }

    /**
     * @inheritDoc
     */
    public function setShortCircuitCurrent($shortCircuitCurrent)
    {
        $this->shortCircuitCurrent = $shortCircuitCurrent;

        return $this;
    }

    /**
     * Get shortCircuitVoltage
     *
     * @return float
     */
    public function getShortCircuitCurrent()
    {
        return $this->shortCircuitCurrent;
    }

    /**
     * Set efficiency
     *
     * @param string $efficiency
     * @return Module
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $this->viewMode ? $efficiency / 100 : $efficiency ;

        return $this;
    }

    /**
     * Get efficiency
     *
     * @return string
     */
    public function getEfficiency()
    {
        return $this->viewMode ? $this->efficiency * 100 : $this->efficiency;
    }

    /**
     * @inheritDoc
     */
    public function setTemperatureOperation($temperatureOperation)
    {
        $this->temperatureOperation = $temperatureOperation;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemperatureOperation()
    {
        return $this->temperatureOperation;
    }

    /**
     * Set tempCoefficientMaxPower
     *
     * @param float $tempCoefficientMaxPower
     * @return Module
     */
    public function setTempCoefficientMaxPower($tempCoefficientMaxPower)
    {
        $this->tempCoefficientMaxPower = $tempCoefficientMaxPower;

        return $this;
    }

    /**
     * Get tempCoefficientMaxPower
     *
     * @return float
     */
    public function getTempCoefficientMaxPower()
    {
        return $this->tempCoefficientMaxPower;
    }

    /**
     * Set cellType
     *
     * @param string $cellType
     * @return Module
     */
    public function setCellType($cellType)
    {
        $this->cellType = $cellType;

        return $this;
    }

    /**
     * Get cellType
     *
     * @return string
     */
    public function getCellType()
    {
        return $this->cellType;
    }

    /**
     * Set cellNumber
     *
     * @param integer $cellNumber
     * @return Module
     */
    public function setCellNumber($cellNumber)
    {
        $this->cellNumber = $cellNumber;

        return $this;
    }

    /**
     * Get cellNumber
     *
     * @return integer
     */
    public function getCellNumber()
    {
        return $this->cellNumber;
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
    public function setTempCoefficientShortCircuitCurrent($tempCoefficientShortCircuitCurrent)
    {
        return $this->setTempCoefficientIsc($tempCoefficientShortCircuitCurrent);
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientShortCircuitCurrent()
    {
        return $this->getTempCoefficientIsc();
    }

    /**
     * @inheritDoc
     */
    public function setTempCoefficientOpenCircuitVoltage($tempCoefficientOpenCircuitVoltage)
    {
        return $this->setTempCoefficientVoc($tempCoefficientOpenCircuitVoltage);
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientOpenCircuitVoltage()
    {
        return $this->getTempCoefficientVoc();
    }

    /**
     * @inheritDoc
     */
    public function setTempCoefficientIsc($tempCoefficientIsc)
    {
        $this->tempCoefficientIsc = $tempCoefficientIsc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientIsc()
    {
        return $this->tempCoefficientIsc;
    }

    /**
     * @inheritDoc
     */
    public function setTempCoefficientVoc($tempCoefficientVoc)
    {
        $this->tempCoefficientVoc = $tempCoefficientVoc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientVoc()
    {
        return $this->tempCoefficientVoc;
    }

    /**
     * @inheritDoc
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @inheritDoc
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @inheritDoc
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeight()
    {
        return $this->height;
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
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function setAvailable($available)
    {
        $this->available = $available;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAvailable()
    {
        return $this->available;
    }


    /**
     * @inheritDoc
     */
    public function isDisable()
    {
        return self::DISABLE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        return self::ACTIVE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public static function getStatusOptions()
    {
        return [
            self::DISABLE  => 'Inativo',
            self::ACTIVE => 'Ativo'
        ];
    }
}

<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\TokenizerTrait;
use AppBundle\Model\Snapshot;
use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table(name="app_component_module")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Module implements ModuleInterface
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
     * @var string
     *
     * @ORM\Column(name="cell_type", type="string", length=100, nullable=true)
     */
    private $cellType;

    /**
     * @var integer
     *
     * @ORM\Column(name="cell_number", type="integer")
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
     * @ORM\Column(name="efficiency", type="float")
     */
    private $efficiency;

    /**
     * @var integer
     *
     * @ORM\Column(name="temperature_operation", type="smallint")
     */
    private $temperatureOperation;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_max_power", type="float")
     */
    private $tempCoefficientMaxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_voc", type="float")
     */
    private $tempCoefficientOpenCircuitVoltage;

    /**
     * @var float
     *
     * @ORM\Column(name="temp_coefficient_isc", type="float")
     */
    private $tempCoefficientShortCircuitCurrent;

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
     * @return string
     */
    public function __toString()
    {
        return $this->getModel();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Module
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Module
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
    public function isModule()
    {
        return $this instanceof ModuleInterface;
    }

    /**
     * @inheritDoc
     */
    public function setTempCoefficientShortCircuitCurrent($tempCoefficientShortCircuitCurrent)
    {
        $this->tempCoefficientShortCircuitCurrent = $tempCoefficientShortCircuitCurrent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientShortCircuitCurrent()
    {
        return $this->tempCoefficientShortCircuitCurrent;
    }

    /**
     * @inheritDoc
     */
    public function setTempCoefficientOpenCircuitVoltage($tempCoefficientOpenCircuitVoltage)
    {
        $this->tempCoefficientOpenCircuitVoltage = $tempCoefficientOpenCircuitVoltage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTempCoefficientOpenCircuitVoltage()
    {
        return $this->tempCoefficientOpenCircuitVoltage;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }
}


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
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Inverter
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_component_inverter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Inverter implements InverterInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

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
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="data_sheet", type="string", nullable=true)
     */
    protected $dataSheet;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    protected $image;

    /**
     * @var MakerInterface
     *
     * @ORM\ManyToOne(targetEntity="Maker", inversedBy="inverters")
     * @ORM\JoinColumn(name="maker")
     */
    protected $maker;

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
    public function setDatasheet($dataSheet)
    {
        $this->dataSheet = $dataSheet;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDatasheet()
    {
        return $this->dataSheet;
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param MakerInterface $maker
     */
    public function setMaker($maker)
    {
        $this->maker = $maker;
    }

    /**
     * @return MakerInterface
     */
    public function getMaker()
    {
        return $this->maker;
    }

}
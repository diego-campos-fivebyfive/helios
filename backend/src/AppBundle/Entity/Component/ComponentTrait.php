<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * class ComponentTrait
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 * @Serializer\ExclusionPolicy("all")
 */
trait ComponentTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="datasheet", type="string", nullable=true)
     */
    protected $datasheet;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    protected $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var MakerInterface
     *
     * @ORM\ManyToOne(targetEntity="Maker")
     * @ORM\JoinColumn(name="maker")
     */
    protected $maker;

    /**
     * @var boolean
     *
     * @ORM\Column(name="promotional", type="boolean", nullable=true)
     */
    protected $promotional;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    protected $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available", type="boolean", nullable=true)
     */
    protected $available;

    /**
     * @var string
     *
     * @ORM\Column(name="nmc", type="string", nullable=true)
     */
    protected $ncm;

    /**
     * @var float
     *
     * @ORM\Column(name="cmv_protheus", type="float", nullable=true)
     */
    protected $cmvProtheus;

    /**
     * @var float
     *
     * @ORM\Column(name="cmv_applied", type="float", nullable=true)
     */
    protected $cmvApplied;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $dependencies;

    /**
     * @var int
     *
     * @Serializer\Expose()
     * @Serializer\Accessor(getter="getMakerId")
     * @Serializer\Groups({"api"})
     */
    protected $makerId;

    /***
     * @param $datasheet
     * @return $this
     */
    public function setDatasheet($datasheet)
    {
        $this->datasheet = $datasheet;
        $this->dependencies = [];

        return $this;
    }

    /***
     * @return string
     */
    public function getDatasheet()
    {
        return $this->datasheet;
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function setMaker(MakerInterface $maker)
    {
        $this->maker = $maker;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaker()
    {
        return $this->maker;
    }

    /**
     * @return int|null
     */
    public function getMakerId()
    {
        return $this->maker ? $this->maker->getId() : null;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return false;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * @return bool
     */
    public function isPromotional()
    {
        return $this->promotional;
    }

    /**
     * @param bool $promotional
     * @return $this
     */
    public function setPromotional($promotional)
    {
        $this->promotional = $promotional;
        return $this;
    }

    /***
     * @param string $ncm
     * @return $this
     */
    public function setNcm($ncm)
    {
        $this->ncm = $ncm;
        return $this;
    }

    /***
     * @return string
     */
    public function getNcm()
    {
        return $this->ncm;
    }

    /***
     * @param string $cmvProtheus
     * @return $this
     */
    public function setCmvProtheus($cmvProtheus)
    {
        $this->cmvProtheus = $cmvProtheus;
        return $this;
    }

    /***
     * @return string
     */
    public function getCmvProtheus()
    {
        return $this->cmvProtheus;
    }

    /***
     * @param $cmvApplied
     * @return $this
     */
    public function setCmvApplied($cmvApplied)
    {
        $this->cmvApplied = $cmvApplied;
        return $this;
    }

    /***
     * @return string
     */
    public function getCmvApplied()
    {
        return $this->cmvApplied;
    }

    /**
     * @inheritDoc
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return (array) $this->dependencies;
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
    public function isAvailable()
    {
        return $this->available;
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
    public function isDisable()
    {
        return self::DISABLE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        return $this->status;
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

<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ComponentTrait
 * @package AppBundle\Entity\Component
 */
trait ComponentTrait
{
    public $viewMode = false;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=100)
     */
    protected $model;

    /**
     * @var ComponentInterface
     */
    protected $parent;

    /**
     * @var AccountInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumn(name="account")
     */
    protected $account;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10)
     */
    protected $status;

    /**
     * @var ArrayCollection
     */
    protected $childrens;

    /**
     * @var string
     */
    protected $datasheet;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $tempDatasheet;

    /**
     * @var string
     */
    protected $tempImage;
    
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
    public function setParent(ComponentInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getDatasheet()
    {
        return $this->datasheet;
    }

    /**
     * @param $datasheet
     * @return $this
     */
    public function setDatasheet($datasheet)
    {
        $this->datasheet = $datasheet;
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
     * @inheritDoc
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getTempDatasheet()
    {
        return $this->tempDatasheet;
    }

    /**
     * @inheritDoc
     */
    public function setTempDatasheet($tempDatasheet)
    {
        $this->tempDatasheet = $tempDatasheet;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTempImage()
    {
        return $this->tempImage;
    }

    /**
     * @inheritDoc
     */
    public function setTempImage($tempImage)
    {
        $this->tempImage = $tempImage;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMaker(MakerInterface $maker)
    {
        /*if(!$maker->isMakerModule())
            Maker::unsupportedMakerContextException();*/

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
     * @inheritDoc
     */
    public function setAccount(BusinessInterface $account = null)
    {
        if(null != $this->id) {
            $this->globalAccountException();
        }

        $this->account = $account;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function addChildren(ComponentInterface $children)
    {
        if(!$this->childrens->contains($children)){
            $this->childrens->add($children);
            $children->setParent($this);
        }

        return $this;
    }

    public function removeChildren(ComponentInterface $children)
    {
        if($this->childrens->contains($children)){
            $this->childrens->removeElement($children);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChildrens()
    {
        return $this->childrens;
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
    public function setStatus($status)
    {
        $this->checkStatusDefinition($status);

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
    public function isFeatured()
    {
        return $this->status == self::STATUS_FEATURED;
    }

    /**
     * @inheritDoc
     */
    public function isIgnored()
    {
        return $this->status == self::STATUS_IGNORED;
    }

    /**
     * @inheritDoc
     */
    public function isValidated()
    {
        return $this->status == self::STATUS_VALIDATED;
    }

    /**
     * @inheritDoc
     */
    public function isPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    /**
     * @inheritDoc
     */
    public function isModule()
    {
        return $this instanceof ModuleInterface;
    }

    /**
     * @return bool
     */
    public function isCopy()
    {
        return $this->parent instanceof ComponentInterface;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->account instanceof BusinessInterface ;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = [
            'id' => $this->id,
            'token' => $this->getToken(),
            'model' => $this->model
        ];

        /**
         * If Inverter
         */
        if(!$this->isModule()) {
            $data['nominal_power'] = $this->getNominalPower();
        }

        return $data;
    }
    
    public function toViewMode()
    {
        $this->viewMode = true;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_FEATURED => self::STATUS_FEATURED,
            self::STATUS_IGNORED => self::STATUS_IGNORED,
            self::STATUS_VALIDATED => self::STATUS_VALIDATED,
            self::STATUS_PUBLISHED => self::STATUS_PUBLISHED
        ];
    }

    /**
     * @param $status
     */
    private function checkStatusDefinition($status)
    {
        if(!array_key_exists($status, self::getStatuses())){
            throw new \InvalidArgumentException(sprintf('Invalid component status definition: %s', $status));
        }
    }

    private function globalAccountException()
    {
        throw new \InvalidArgumentException(ComponentInterface::ERROR_GLOBAL_ACCOUNT);
    }
}
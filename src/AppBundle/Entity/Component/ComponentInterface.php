<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ComponentInterface
{
    const ERROR_GLOBAL_ACCOUNT = 'Global component does not support account reference';

    const STATUS_FEATURED   = 'featured';
    const STATUS_IGNORED    = 'ignored';
    const STATUS_VALIDATED  = 'validated';
    const STATUS_PUBLISHED  = 'published';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param ComponentInterface $parent
     * @return ComponentInterface
     */
    public function setParent(ComponentInterface $parent);

    /**
     * @return ComponentInterface
     */
    public function getParent();

    /**
     * @param MakerInterface $maker
     * @return ModuleInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();

    /**
     * @param BusinessInterface $account
     * @return ComponentInterface|ModuleInterface|InverterInterface|null
     */
    public function setAccount(BusinessInterface $account = null);

    /**
     * @return BusinessInterface
     */
    public function getAccount();

    /**
     * @param ComponentInterface | ModuleInterface | InverterInterface $children
     * @return ComponentInterface
     */
    public function addChildren(ComponentInterface $children);

    /**
     * @param ComponentInterface | ModuleInterface | InverterInterface $children
     * @return ComponentInterface
     */
    public function removeChildren(ComponentInterface $children);

    /**
     * @return ArrayCollection
     */
    public function getChildrens();
    
    /**
     * @param $model
     * @return ComponentInterface
     */
    public function setModel($model);

    /**
     * @return string
     */
    public function getModel();

    /**
     * @param $status
     * @return ComponentInterface
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return bool status == self::STATUS_FEATURED
     */
    public function isFeatured();

    /**
     * @return bool status == self::STATUS_IGNORED
     */
    public function isIgnored();

    /**
     * @return bool status == self::STATUS_VALIDATED
     */
    public function isValidated();

    /**
     * @return bool status == self::STATUS_PUBLISHED
     */
    public function isPublished();

    /**
     * @param $datasheet
     * @return ComponentInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return ComponentInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();
    
    /**
     * @return bool
     */
    public function isModule();

    /**
     * @return bool
     */
    public function isCopy();

    /**
     * @return bool
     */
    public function isPrivate();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return array
     */
    public function snapshot();
}
<?php

namespace AppBundle\Entity\Component;

/**
 * Interface ComponentInterface
 */
interface ComponentInterface
{
    const DISABLE = 0;
    const ACTIVE = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $datasheet
     * @return mixed
     */
    public function setDatasheet($datasheet);

    /**
     * @return mixed
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return mixed
     */
    public function setImage($image);

    /**
     * @return mixed
     */
    public function getImage();

    /**
     * @param $position
     * @return mixed
     */
    public function setPosition($position);

    /**
     * @return mixed
     */
    public function getPosition();

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * @param MakerInterface $maker
     * @return mixed
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return mixed
     */
    public function getMaker();

    /**
     * @return mixed
     */
    public function isPublished();

    /**
     * @return mixed
     */
    public function isPromotional();

    /**
     * @param $promotional
     * @return mixed
     */
    public function setPromotional($promotional);

    /**
     * @param $ncm
     * @return mixed
     */
    public function setNcm($ncm);

    /**
     * @return mixed
     */
    public function getNcm();

    /**
     * @param $cmvProtheus
     * @return mixed
     */
    public function setCmvProtheus($cmvProtheus);

    /**
     * @return mixed
     */
    public function getCmvProtheus();

    /**
     * @param $cmvApplied
     * @return mixed
     */
    public function setCmvApplied($cmvApplied);

    /**
     * @return mixed
     */
    public function getCmvApplied();

    /**
     * @param $dependencies
     * @return ComponentInterface
     */
    public function setDependencies($dependencies);

    /**
     * @return array
     */
    public function getDependencies();

    /**
     * @param int $stock
     * @return ComponentInterface
     */
    public function setStock($stock);

    /**
     * @return int
     */
    public function getStock();

    /**
     * @param $available
     * @return ModuleInterface
     */
    public function setAvailable($available);

    /**
     * @return boolean
     */
    public function getAvailable();

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return bool
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function isDisable();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isSalable();

    /**
     * @return string
     */
    public function getFamily();

    /**
     * @return array
     */
    public static function getStatusOptions();

    /**
     * @param $princingLevels
     * @return mixed
     */
    public function setPrincingLevels($princingLevels);

    /**
     * @return array
     */
    public function getPrincingLevels();

    /**
     * @param $generatorLevels
     * @return mixed
     */
    public function setGeneratorLevels($generatorLevels);

    /**
     * @return array
     */
    public function getGeneratorLevels();

    /**
     * @param $status
     * @param $quantity
     * @return ComponentInterface
     */
    public function setOrderInventory($status, $quantity);

    /**
     * @param $status
     * @return array
     */
    public function getOrderInventory($status = null);
}

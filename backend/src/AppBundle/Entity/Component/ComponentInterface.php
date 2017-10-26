<?php

namespace AppBundle\Entity\Component;

/**
 * Interface ComponentInterface
 */
interface ComponentInterface
{
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
}

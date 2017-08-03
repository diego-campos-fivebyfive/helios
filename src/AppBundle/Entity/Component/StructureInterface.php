<?php

namespace AppBundle\Entity\Component;

interface StructureInterface
{
    const DISABLE = 0;
    const ACTIVE = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $code
     * @return mixed
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getCode();

    /**
    * @param $type
    * @return mixed
     */
    public function setType($type);

    /**
     *  @return mixed
    */
    public function getType();

    /**
     *  @param $subtype
     *  @return mixed
     */
    public function setSubType($subtype);

    /**
     * @return mixed
     */
    public function getSubType();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $size
     * @return mixed
     */
    public function setSize($size);

    /**
      * @return mixed
      */
    public function getSize();

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return mixed
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
     * @param $datasheet
     * @return StructureInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return StructureInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return array
     */
    public static function getStatusOptions();

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
     * @return StructureInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();
}

<?php

namespace AppBundle\Entity\Component;

interface StructureInterface extends ComponentInterface
{
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
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();
}

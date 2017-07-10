<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 10/07/17
 * Time: 12:05
 */

namespace AppBundle\Entity\Pricing;


interface MemorialInteface
{

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $version
     * @return mixed
     */
    public function setVersion($version);

    /**
     * @return mixed
     */
    public function getVersion();

    /**
     * @param $startAt
     * @return mixed
     */
    public function setStartAt($startAt);

    /**
     * @return mixed
     */
    public function getStartAt();

    /**
     * @param $endAt
     * @return mixed
     */
    public function setEndAt($endAt);

    /**
     * @return mixed
     */
    public function getEndAt();

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getStatus();
}
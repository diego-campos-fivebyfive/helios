<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 10/07/17
 * Time: 12:05
 */

namespace AppBundle\Entity\Pricing;


interface MemorialInterface
{
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

    /**
     * @param $isquikId
     * @return MemorialInterface
     */
    public function setIsquikId($isquikId);

    /**
     * @return int
     */
    public function getIsquikId();

    /**
     * @return float
     */
    public function getTax();

    /**
     * @param RangeInterface $range
     * @return RangeInterface
     */
    public function addRange(RangeInterface $range);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRanges();

    /**
     * @return array
     */
    public function toArray();
}
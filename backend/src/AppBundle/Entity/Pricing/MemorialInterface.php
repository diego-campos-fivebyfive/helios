<?php

namespace AppBundle\Entity\Pricing;

interface MemorialInterface
{
    const STATUS_PENDING = 0;
    const STATUS_ENABLED = 1;
    const STATUS_EXPIRED = 2;

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
     * @param $expiredAt
     * @return MemorialInterface
     */
    public function setExpiredAt(\DateTime $expiredAt);

    /**
     * @return mixed
     */
    public function getExpiredAt();

    /**
     * @param \DateTime $publishedAt
     * @return MemorialInterface
     */
    public function setPublishedAt(\DateTime $publishedAt);

    /**
     * @return \DateTime
     */
    public function getPublishedAt();

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
    public function isExpired();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param $name
     * @return MemorialInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

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
     * @param array $levels
     * @return MemorialInterface
     */
    public function setLevels(array $levels);

    /**
     * @return array
     */
    public function getLevels();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}

<?php

namespace AppBundle\Entity\Pricing;

interface MemorialInterface
{
    const STATUS_PENDING = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_EXPIRED = 2;

    const LEVEL_BLACK = 'black';
    const LEVEL_GOLD = 'gold';
    const LEVEL_PARTNER = 'partner';
    const LEVEL_PLATINUM = 'platinum';
    const LEVEL_PREMIUM = 'premium';
    const LEVEL_PROMOTIONAL = 'promotional';

    /**
     * @return int
     */
    public function getId();

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
    public function getStatus($label = false);

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
    public function isPublished();

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

    /**
     * @return array
     */
    public static function getStatuses();

    /**
     * @return array
     */
    public static function getDefaultLevels($keys = false);
}

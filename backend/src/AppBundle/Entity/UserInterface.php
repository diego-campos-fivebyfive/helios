<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    const ROLE_OWNER = 'ROLE_OWNER';
    const ROLE_OWNER_MASTER = 'ROLE_OWNER_MASTER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_AGENT = 'ROLE_AGENT';
    const ACTIVITY_DELAY = '5 minutes ago';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $isquik_id
     * @return UserInterface
     */
    public function setIsquikId($isquik_id);

    /**
     * @return integer
     */
    public function getIsquikId();

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isSame(UserInterface $user);

    /**
     * @return bool
     */
    public function isAdmin();

    /**
     * @return bool
     */
    public function isOwner();

    /**
     * @return bool
     */
    public function isOwnerMaster();

    /**
     * @return BusinessInterface | null
     */
    public function getInfo();

    /**
     * @param \DateTime $lastActivity
     * @return UserInterface
     */
    public function setLastActivity(\DateTime $lastActivity);

    /**
     * @return \DateTime
     */
    public function getLastActivity();

    /**
     * @param bool $status
     * @return bool
     */
    public function isOnline($status = false);

    /**
     * @param \DateTime $created_at
     * @return UserInterface
     */
    public function setCreatedAt($created_at);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updated_at
     * @return UserInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
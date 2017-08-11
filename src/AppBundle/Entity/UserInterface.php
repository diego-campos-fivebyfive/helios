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
}

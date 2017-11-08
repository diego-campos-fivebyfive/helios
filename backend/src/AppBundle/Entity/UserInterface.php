<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    const TYPE_PLATFORM = 'platform';
    const TYPE_ACCOUNT = 'account';

    const ROLE_OWNER = 'ROLE_OWNER';
    const ROLE_OWNER_MASTER = 'ROLE_OWNER_MASTER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_AGENT = 'ROLE_AGENT';
    const ACTIVITY_DELAY = '5 minutes ago';

    const ROLE_PLATFORM_MASTER = 'ROLE_PLATFORM_MASTER';
    const ROLE_PLATFORM_ADMIN  = 'ROLE_PLATFORM_ADMIN';
    const ROLE_PLATFORM_COMMERCIAL = 'ROLE_PLATFORM_COMMERCIAL';
    const ROLE_PLATFORM_FINANCIAL = 'ROLE_PLATFORM_FINANCIAL';
    const ROLE_PLATFORM_AFTER_SALES = 'ROLE_PLATFORM_AFTER_SALES';
    const ROLE_PLATFORM_EXPANSE = 'ROLE_PLATFORM_EXPANSE';
    const ROLE_PLATFORM_LOGISTIC = 'ROLE_PLATFORM_LOGISTIC';

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
    public function isPlatform();

    /**
     * @return bool
     */
    public function isPlatformMaster();

    /**
     * @return bool
     */
    public function isPlatformAdmin();

    /**
     * @return bool
     */
    public function isPlatformCommercial();

    /**
     * @return bool
     */
    public function isPlatformFinancial();

    /**
     * @return bool
     */
    public function isPlatformAfterSales();

    /**
     * @return bool
     */
    public function isPlatformExpanse();

    /**
     * @return bool
     */
    public function isPlatformLogistic();

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
     * @return string
     */
    public function getType();

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

    /**
     * @return array
     */
    public static function getRolesOptions();

    /**
     * @return array
     */
    public static function getPlatformRoles();
}

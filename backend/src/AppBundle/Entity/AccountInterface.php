<?php

namespace AppBundle\Entity;

/**
 * Interface AccountInterface
 * This interface is exclusively account properties and methods
 */
interface AccountInterface
{
    # Representative key for monthly counting of projects in free accounts
    const ATTR_PROJECTS_COUNT = 'projects_count';

    # Represents the attribute for quota of projects in free accounts
    const ATTR_PROJECTS_QUOTA = 'projects_quota';

    # Default quota of monthly projects for free accounts
    const PROJECTS_QUOTA = 4;

    const MAX_MEMBERS = 5;
    
    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return bool
     */
    public function isStanding();

    /**
     * @return bool
     */
    public function isAproved();

    /**
     * @return bool
     */
    public function isActivated();

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @return MemberInterface
     */
    public function getOwner();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return int
     */
    public function getMaxMembers();


    /**
     * @param $maxMember
     * @return BusinessInterface
     */
    public function setMaxMember($maxMember);

    /**
     * @param MemberInterface $member
     * @return AccountInterface
     */
    public function addMember(MemberInterface $member);

    /**
     * @param MemberInterface $member
     * @return AccountInterface
     */
    public function removeMember(MemberInterface $member);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembers();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getActiveMembers();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getInactiveMembers();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getInvitedMembers();

    /**
     * @param null $confirmationToken
     * @return AccountInterface
     */
    public function setConfirmationToken($confirmationToken = null);

    /**
     * @return string|null
     */
    public function getConfirmationToken();

    /**
     * @param $level
     * @return AccountInterface
     */
    public function setLevel($level);

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @param $isquik_id
     * @return AccountInterface
     */
    public function setIsquikId($isquik_id);

    /**
     * @return integer
     */
    public function getIsquikId();

    /**
     * Checks whether the account is free or has a linked subscription
     *
     * @return bool
     */
    public function isFreeAccount();

    /**
     * Returns the count of projects in the current month
     * present in the attribute group
     *
     * @return int
     */
    public function getProjectsCount();

    /**
     * Returns the monthly quota of projects for the account
     * Accounts with signature always return the default quota
     * If the quota is not defined in the attributes, it returns the default quota
     *
     * @return int
     */
    public function getProjectsQuota();

    /**
     * Checks that the monthly project quota has been reached.
     * Available only for free accounts
     * Signed accounts will always return false
     *
     * @return bool
     */
    public function projectsQuotaIsReached();

    /**
     * Increment the count of projects for quota control
     *
     * @param int $count
     * @return AccountInterface
     */
    public function incrementProjectsCount($count = 1);

    /**
     * @param $key
     * @param $value
     * @return BusinessInterface
     */
    public function setAttribute($key, $value);
}

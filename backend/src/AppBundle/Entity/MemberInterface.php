<?php

namespace AppBundle\Entity;

interface MemberInterface
{
    const CONTEXT = 'member';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $isquik_id
     * @return MemberInterface
     */
    public function setIsquikId($isquik_id);

    /**
     * @return integer
     */
    public function getIsquikId();

    /**
     * @param $timezone
     * @return MemberInterface
     */
    public function setTimezone($timezone);

    /**
     * @param null $confirmationToken
     * @return MemberInterface
     */
    public function setConfirmationToken($confirmationToken = null);

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
    public function isPlatformUser();

    /**
     * @return string
     */
    public function getUserType();

    /**
     * @return bool
     */
    public function isOwner();

    /**
     * @return bool
     */
    public function isMasterOwner();

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param AccountInterface $account
     * @return MemberInterface
     */
    public function setAccount(AccountInterface $account);

    /**
     * @return AccountInterface
     */
    public function getAccount();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAllowedContacts();
}

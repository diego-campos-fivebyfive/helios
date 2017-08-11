<?php

namespace AppBundle\Entity;

interface MemberInterface
{
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
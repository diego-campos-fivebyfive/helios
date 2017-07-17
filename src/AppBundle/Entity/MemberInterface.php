<?php

namespace AppBundle\Entity;

interface MemberInterface
{
    /**
     * @return int
     */
    public function getId();

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
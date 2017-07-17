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
}
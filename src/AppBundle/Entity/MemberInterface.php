<?php

namespace AppBundle\Entity;

interface MemberInterface
{
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
<?php

namespace AppBundle\Entity\Extra;

use Sonata\CoreBundle\Model\BaseEntityManager;

class AccountRegisterManager extends BaseEntityManager
{
    public function findRegisterByConfirmationToken($token)
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }
}
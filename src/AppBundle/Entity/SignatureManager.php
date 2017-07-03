<?php

namespace AppBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;

class SignatureManager extends BaseEntityManager
{
    public function sign(BusinessInterface $account, $subscription)
    {
        if(null == $signature = $this->findOneBy(['account' => $account ])){

            /** @var SignatureInterface $signature */
            $signature = $this->create();
            $signature->setAccount($account);
        }

        $signature->setContent($subscription);

        $this->save($signature);

        return $signature;
    }
}
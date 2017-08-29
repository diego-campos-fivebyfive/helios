<?php

namespace AppBundle\Manager;

use AppBundle\Entity\BusinessInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

class AccountManager extends BaseEntityManager
{
    /**
     * @param BusinessInterface $account
     * @param $index
     * @return BusinessInterface
     */
    public function incrementIndex(BusinessInterface $account, $index)
    {
        $nextIndex = $account->getAttribute($index, 0)+1;
        $account->addAttribute($index, $nextIndex);

        $this->save($account);

        return $nextIndex;
    }
}
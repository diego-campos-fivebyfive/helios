<?php

namespace AppBundle\Service\Util;

use AppBundle\Entity\AccountInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\CustomerManager;

class AccountTransfer
{

    /**
     * @var AccountManager
     */
    private $manager;

    function __construct(CustomerManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    public function transfer($source, $target)
    {
        /** @var AccountInterface $accountManager */
        $accountManager = $this->customerManager;

        /** @var AccountInterface $accounts */
        $accounts = $accountManager->findBy([
            'context' => 'account',
            'agent' => $source
        ]);

        foreach ($accounts as $account) {

            if ($account->getAgent() == $source) {
                $account->setAgent($target);

                $accountManager->save($account);
            }
        }
    }

}

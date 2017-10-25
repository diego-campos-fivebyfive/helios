<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AppBundle\Service\Util;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\CustomerManager;

/**
 * Transform project data into a new order
 *
 * @author João Zaqueu Chereta <joaozaqueuchereta@gmail.com>
 */
class AccountTransfer
{
    /**
     * @var AccountManager
     */
    private $manager;

    /**
     * AccountTransfer constructor.
     * @param CustomerManager $manager
     */
    function __construct(CustomerManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param MemberInterface $source
     * @param MemberInterface $target
     */
    public function transfer(MemberInterface $source, MemberInterface $target)
    {
        $this->check($source, $target);

        /** @var AccountInterface $accounts */
        $accounts = $this->manager->findBy([
            'context' => 'account',
            'agent' => $source
        ]);

        foreach ($accounts as $key => $account) {

                $account->setAgent($target);
                $this->manager->save($account, ($key == count($accounts) - 1));
        }
    }

    /**
     * @param MemberInterface $source
     * @param MemberInterface $target
     */
    private function check(MemberInterface $source, MemberInterface $target)
    {
        if ($source == $target) {
            throw new \InvalidArgumentException('Operação não permitida');
        }

        if (!$target->isPlatformCommercial()) {
            throw new \InvalidArgumentException('Este usuário não é Comercial');
        }
    }

}

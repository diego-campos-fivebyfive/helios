<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\Transaction;
use AppBundle\Manager\Stock\TransactionManager;

/**
 * Class Provider
 * This class generate transactions based in defined operations
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Control
{
    /**
     * @var TransactionManager
     */
    private $manager;

    /**
     * @var array
     */
    private $operations = [];

    /**
     * Control constructor.
     * @param TransactionManager $manager
     */
    function __construct(TransactionManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Operation $operation
     * @return $this
     */
    public function addOperation(Operation $operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * @param array $operations
     * @return $this
     */
    public function setOperations(array $operations = [])
    {
        foreach ($operations as $operation){
            $this->addOperation($operation);
        }

        return $this;
    }

    /**
     * @param array $operations
     */
    public function process(array $operations = [])
    {
        if(!empty($operations))
            $this->setOperations($operations);

        /** @var Operation $operation */
        foreach ($this->operations as $operation){
            $this->transact($operation);
        }

        $this->commit();
    }

    /**
     * @param Operation $operation
     */
    public function transact(Operation $operation, $commit = false)
    {
        $transaction = new Transaction();

        $transaction
            ->setFamily($operation->getFamily())
            ->setIdentity($operation->getIdentity())
            ->setAmount($operation->getAmount())
            ->setDescription($operation->getDescription())
        ;

        $this->manager->save($transaction, $commit);
    }

    /**
     * Commit all transactions
     */
    public function commit()
    {
        $this->manager->getEntityManager()->flush();
        $this->operations = [];
    }
}

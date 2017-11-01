<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Entity\Stock\Transaction;
use AppBundle\Manager\Stock\ProductManager;

/**
 * Class Provider
 * This class generate transactions based in defined operations
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Control
{
    /**
     * @var ProductManager
     */
    private $manager;

    /**
     * @var array
     */
    private $operations = [];

    /**
     * Control constructor.
     * @param ProductManager $manager
     */
    function __construct(ProductManager $manager)
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
            ->setProduct($operation->getProduct())
            ->setAmount($operation->getAmount())
            ->setDescription($operation->getDescription())
        ;

        $this->manager->save($operation->getProduct(), $commit);
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

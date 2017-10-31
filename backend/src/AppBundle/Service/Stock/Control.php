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
     * @param ProductInterface $product
     * @param $amount
     * @param $description
     * @return $this
     */
    public function addOperation(ProductInterface $product, $amount, $description)
    {
        if(!is_int($amount)) throw new \InvalidArgumentException('Invalid amount value type');

        $this->operations[] = [$product, $amount, $description];

        return $this;
    }

    /**
     * @param array $operations
     * @return $this
     */
    public function setOperations(array $operations = [])
    {
        foreach ($operations as $operation){
            list($product, $amount, $description) = $operation;
            $this->addOperation($product, $amount, $description);
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

        foreach ($this->operations as $operation){

            list($product, $amount, $description) = $operation;

            $this->transact($product, $amount, $description);
        }

        $this->commit();
    }

    /**
     * @param ProductInterface $product
     * @param $amount
     * @param $description
     */
    public function transact(ProductInterface $product, $amount, $description, $commit = false)
    {
        $transaction = new Transaction();
        $transaction
            ->setProduct($product)
            ->setAmount($amount)
            ->setDescription($description)
        ;

        $this->manager->save($product, $commit);
    }

    /**
     * Commit all transactions
     */
    public function commit()
    {
        $this->manager->getEntityManager()->flush();
    }
}

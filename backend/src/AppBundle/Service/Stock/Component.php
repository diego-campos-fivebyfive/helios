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

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Stock\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Component
 * This class process stock transactions for components
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Component
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * StockControl constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @param ComponentInterface $component
     * @param $amount
     * @param $description
     * @return $this
     */
    public function add(ComponentInterface $component, $amount, $description)
    {
        $this->transactions[] = [
            'component' => $component,
            'amount' => $amount,
            'description' => $description
        ];

        return $this;
    }

    /**
     * @param array $transactions
     */
    public function transact(array $transactions = [])
    {
        if(!empty($transactions))
            $this->registerTransactions($transactions);

        $this->normalizeTransactions();
        $this->filterProducts();

        $operations = [];
        foreach ($this->transactions as $transaction){

            $operation = Operation::create(
                $transaction['product'],
                $transaction['amount'],
                $transaction['description']
            );

            $operations[] = $operation;
        }

        $stockControl = $this->container->get('stock_control');

        $stockControl->process($operations);

        $this->refresh();

        $this->transactions = [];
    }

    /**
     * Refresh stock components
     */
    public function refresh()
    {
        foreach ($this->transactions as $transaction){

            /** @var ComponentInterface $component */
            $component = $transaction['component'];

            /** @var ProductInterface $product */
            $product = $transaction['product'];

            $component->setStock($product->getStock());

            $this->em->persist($component);
        }

        $this->em->flush();
    }

    /**
     * @param array $transactions
     */
    private function registerTransactions(array $transactions)
    {
        foreach ($transactions as $transaction){
            $this->add(
                $transaction['component'],
                $transaction['amount'],
                $transaction['description']
            );
        }
    }

    /**
     * Normalize
     */
    private function normalizeTransactions()
    {
        foreach ($this->transactions as &$transaction){
            $transaction['identity'] = Identity::create($transaction['component']);
        }
    }

    /**
     * Filter products
     */
    private function filterProducts()
    {
        $components = array_map(function($transaction){
            return $transaction['component'];
        }, $this->transactions);

        $products = $this->getConverter()->transform($components);

        $ids = array_map(function(ProductInterface $product){
            return $product->getId();
        }, $products);

        $products = array_combine($ids, $products);

        foreach ($this->transactions as $key => $transaction){
            $this->transactions[$key]['product'] = $products[$transaction['identity']];
        }
    }

    /**
     * @return Converter|object
     */
    private function getConverter()
    {
        return $this->container->get('stock_converter');
    }
}

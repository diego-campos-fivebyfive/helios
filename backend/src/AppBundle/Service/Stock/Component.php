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

/**
 * Class Component
 * This class process stock transactions for components
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Component
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * Component constructor.
     * @param Provider $provider
     */
    function __construct(Provider $provider)
    {
        $this->provider = $provider;
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

        $this->provider->get('stock_control')->process($operations);

        $this->refresh();

        $this->transactions = [];
    }

    /**
     * Refresh stock components
     */
    public function refresh()
    {
        $em = $this->provider->get('em');

        foreach ($this->transactions as $transaction){

            /** @var ComponentInterface $component */
            $component = $transaction['component'];

            /** @var ProductInterface $product */
            $product = $transaction['product'];

            $component->setStock($product->getStock());

            $em->persist($component);
        }

        $em->flush();
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

        $products = $this->provider->get('stock_converter')->transform($components);

        $ids = array_map(function(ProductInterface $product){
            return $product->getId();
        }, $products);

        $products = array_combine($ids, $products);

        foreach ($this->transactions as $key => $transaction){
            $this->transactions[$key]['product'] = $products[$transaction['identity']];
        }
    }
}

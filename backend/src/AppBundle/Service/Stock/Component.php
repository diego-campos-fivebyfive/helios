<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Service\Stock\Converter;
use AppBundle\Service\Stock\Provider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Component
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * StockControl constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function transact(array $transactions)
    {
        $this->normalizeTransactions($transactions);

        $products = $this->filterProducts($transactions);

        dump($products); die;

        //$converter = $this->getConverter();
        //$products = $converter->transform($components);

        //dump($products); die;
        //dump($converter); die;
        dump($transactions); die;
    }

    /**
     * @param array $components
     */
    public function update(array $components)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $this->container->get('doctrine')->getManager();

        foreach ($components as $component) {
            $product = $this->getProduct($component);
            $component->setStock($product->getStock());
            $em->persist($component);
        }

        $em->flush();
    }

    /**
     * @param array $transactions
     */
    private function normalizeTransactions(array &$transactions)
    {
        foreach ($transactions as &$transaction){
            $transaction['identity'] = Identity::create($transaction['component']);
        }
    }

    /**
     * @param array $transactions
     * @return array
     */
    private function filterProducts(array &$transactions)
    {
        $components = array_map(function($transaction){
            return $transaction['component'];
        }, $transactions);

        $products = $this->getConverter()->transform($components);

        $ids = array_map(function(ProductInterface $product){
            return $product->getId();
        }, $products);

        return array_combine($ids, $products);
    }

    /**
     * @return Converter
     */
    private function getConverter()
    {
        return $this->container->get('stock_converter');
    }

    /**
     * @param ComponentInterface $component
     * @return \AppBundle\Entity\Stock\ProductInterface|array|null
     */
    private function getProduct(ComponentInterface $component)
    {
        return $this->getConverter()->transform($component);
    }
}

<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Stock\Product;
use AppBundle\Entity\Stock\ProductInterface;

class Converter
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * Converter constructor.
     * @param Provider $provider
     */
    function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param array $components
     * @return array
     */
    public function transform(array $components)
    {
        $identities = Identity::create($components);

        $this->cacheIdentities($identities);
        $this->cacheComponents($components);
        $this->cacheProducts($components);

        $products = array_values(array_map(function($data){
            return $data['product'];
        }, $this->cache));

        return $products;
    }

    /**
     * @param string|object|array $product
     * @return null|object|array
     */
    public function reverse($product)
    {
        if(is_array($product)){
            $components = [];
            foreach ($product as $item){
                $components[] = $this->reverse($item);
            }

            return $components;
        }

        if($product instanceof ProductInterface)
            $product = $product->getId();

        list($class, $id) = explode('::', $product);

        if(array_key_exists($class, $this->reversed) && array_key_exists($id, $this->reversed[$class]))
            return $this->reversed[$class][$id];

        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $this->provider->get('em');

        $this->benchmark['total_queries'] += 1;

        $component = $em->getRepository($class)->find($id);

        $this->reversed[$class][$id] = $component;

        return $component;
    }

    /**
     * @param array $identities
     */
    private function cacheIdentities(array $identities)
    {
        foreach ($identities as $identity){
            if(!array_key_exists($identity, $this->cache)){
                $this->cache[$identity] = [
                    'component' => null,
                    'product' => null
                ];
            }
        }
    }

    /**
     * @param array $components
     */
    private function cacheComponents(array $components)
    {
        foreach ($components as $component){
            $identity = Identity::create($component);
            if(!$this->cache[$identity]['component']) $this->cache[$identity]['component'] = $component;
        }
    }

    /**
     * 1. Check product cache
     * 2. Find uncached products
     * 3. Create not found products
     * 4. Cache products
     */
    private function cacheProducts()
    {
        $identities = [];
        foreach ($this->cache as $identity => $data){
            if(!$data['product']) $identities[] = $identity;
        }

        if(!empty($identities)) {

            $products = $this->findProducts($identities);

            foreach ($products as $product){

                $this->cache[$product->getId()]['product'] = $product;

                $index = array_search($product->getId(), $identities);
                unset($identities[$index]);
            }
        }

        if(!empty($identities)){

            $manager = $this->provider->get('stock_product_manager');

            foreach ($identities as $key => $identity){

                /** @var ComponentInterface $component */
                $component = $this->cache[$identity]['component'];

                /** @var ProductInterface $product */
                $product = $manager->create();

                $product
                    ->setId($identity)
                    ->setCode($component->getCode())
                    ->setDescription($component->getDescription())
                ;

                $manager->save($product);

                $this->cache[$identity]['product'] = $product;
            }
        }
    }

    /**
     * @param array $identities
     * @return array
     */
    private function findProducts(array $identities)
    {
        $em = $this->getManager();
        $qb = $em->createQueryBuilder();

        $qb
            ->select('p')
            ->from(Product::class, 'p')
            ->where($qb->expr()->in('p.id', $identities))
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $identities
     * @return array
     */
    public function findComponents(array $identities)
    {
        $identities = $this->normalizeIdentities($identities);

        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $this->provider->get('em');

        foreach ($identities as $class => $ids){
            foreach ($ids as $id => $component){
                if(!is_null($component)){
                    unset($identities[$class][$id]);
                }
            }
        }

        foreach ($identities as $class => $ids){

            $qb = $em->createQueryBuilder();

            $components = $qb
                ->select('c')
                ->from($class, 'c')
                ->where($qb->expr()->in('c.id', array_keys($ids)))
                ->getQuery()
                ->getResult()
            ;

            /** @var ComponentInterface $component */
            foreach ($components as $component){
                $identities[$class][$component->getId()] = $component;
            }
        }

        return $identities;
    }

    /**
     * @param $id
     * @return ProductInterface|null
     */
    private function find($id)
    {
        return $this->provider->manager('stock_product')->find($id);
    }

    /**
     * @param array $identities
     * @return array
     */
    private function normalizeIdentities(array $identities)
    {
        $normalized = [];
        foreach ($identities as $identity){

            if($identity instanceof ProductInterface)
                $identity = $identity->getId();

            list($class, $id) = explode('::', $identity);

            if(!array_key_exists($class, $normalized))
                $normalized[$class] = [];

            $normalized[$class][$id] = null;
        }

        return $normalized;
    }

    /**
     * @return object|\Doctrine\ORM\EntityManagerInterface
     */
    private function getManager()
    {
        return $this->provider->get('em');
    }
}

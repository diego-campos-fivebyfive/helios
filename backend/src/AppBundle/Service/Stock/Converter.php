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
use AppBundle\Entity\Stock\Product;
use AppBundle\Entity\Stock\ProductInterface;

/**
 * Class Converter
 * This class convert components to controllable products
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
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
        $this->cacheProducts();

        $products = array_values(array_map(function($data){
            return $data['product'];
        }, $this->cache));

        return $products;
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
     * 2. Find un cached products
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

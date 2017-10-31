<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Component\ComponentInterface;
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
    private $benchmark = [
        'total_queries' => 0
    ];

    /**
     * @var array
     */
    private $reversed = [];

    /**
     * Converter constructor.
     * @param Provider $provider
     */
    function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param object|array $source
     * @return ProductInterface|array|null
     */
    public function transform($source)
    {
        if($source instanceof ProductInterface)
            throw new \InvalidArgumentException('Invalid source object instance.');

        if(is_array($source)){
            $products = [];
            foreach ($source as $item){
                $products[] = $this->transform($item);
            }

            return $products;
        }

        $id = Identity::create($source);

        if(null == $product = $this->find($id)){

            $manager = $this->provider->manager('stock_product');
            /** @var ProductInterface $product */
            $product = $manager->create();
            $product
                ->setId($id)
                ->setCode($source->getCode())
                ->setDescription($source->getDescription())
            ;

            $manager->save($product);
        }

        return $product;
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
     * @return int
     */
    public function totalQueries()
    {
        return $this->benchmark['total_queries'];
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
}

<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Entity\Stock\Transaction;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Processor
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /*public function output($source, $quantity = null)
    {
        $id = Identity::create($source);

        $manager = $this->container->get('stock_product_manager');
        $product = $manager->find($id);

        if(!$product instanceof ProductInterface){

            /** @var ProductInterface $product *
            $product = $manager->create();
            $product
                ->setId($id)
                ->setDescription($source->getDescription())
            ;

            $manager->save($product);
        }

        //$transaction = new Transaction();
        //$transaction
    }*/
}

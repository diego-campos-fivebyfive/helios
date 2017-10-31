<?php

namespace AppBundle\Service\Component;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Service\Stock\Converter;
use AppBundle\Service\Stock\Provider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StockControl
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
     * @return Converter
     */
    private function getConverter()
    {
        /** @var Provider $provider */
        $provider = $this->container->get('stock_provider');

        return new Converter($provider);
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

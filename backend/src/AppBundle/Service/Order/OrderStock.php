<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Service\Stock\Converter;
use AppBundle\Service\Stock\Identity;
use Doctrine\ORM\EntityManagerInterface;

class OrderStock
{
    /**
     * @var ComponentCollector
     */
    private $collector;
    /**
     * @var Converter
     */
    private $converter;

    /**
     * OrderStock constructor.
     * @param ComponentCollector $collector
     */
    function __construct(ComponentCollector $collector, Converter $converter)
    {
        $this->collector = $collector;
        $this->converter = $converter;
    }

    public function debit(OrderInterface $order)
    {
        $components = $this->mappingComponents($order);
        $products = $this->converter->transform($components);



        dump($products); die;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function mappingComponents(OrderInterface $order)
    {
        $components = [];
        foreach ($order->getElements() as $element){
            $component = $this->collector->fromCode($element->getCode());
            if($component) {
                $components[] = $component;
            }
        }

        return $components;
    }
}

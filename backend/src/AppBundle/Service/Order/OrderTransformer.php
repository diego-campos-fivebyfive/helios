<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Manager\OrderManager;

/**
 * Transform project data into a new order
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderTransformer
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * OrderTransformer constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ProjectInterface $project
     * @param bool $persist
     * @return OrderInterface
     */
    public function transformFromProject(ProjectInterface $project, $persist = true)
    {
        /** @var OrderInterface $order */
        $order = $this->manager->create();

        $collection = ComponentExtractor::fromProject($project);

        foreach ($collection as $data){
            $element = ElementResolver::create($data);
            $order->addElement($element);
        }

        $order->setDescription(sprintf('Sistema de %skWp', $project->getPower()));
        $order->setShippingRules($project->getShippingRules());
        $order->setInsurance($project->getInsurance());

        if($project->isPromotional()){
            $order
                ->setDescription($order->getDescription() . ' [promo]')
                ->setLevel(MemorialInterface::LEVEL_PROMOTIONAL);
        }

        if($persist) $this->manager->save($order);

        return $order;
    }

    /**
     * @param array $childrens
     * @return OrderInterface
     */
    public function transformFromChildrens(array $childrens)
    {
        $this->normalizeChildrens($childrens);

        /** @var OrderInterface $order */
        $order = $this->manager->create();
        foreach ($childrens as $children){
            if(!$children->getParent()) {
                $order->addChildren($children);
                if (!$order->getAccount() && $children->getAccount()) {
                    $order->setAccount($children->getAccount());
                }
            }
        }

        $metadata = $order->getChildrens()->first()->getMetadata();

        $order->setMetadata($metadata);

        $this->manager->save($order);

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param $code
     * @param $description
     * @param $unitPrice
     * @param $quantity
     */
    private function addOrderElement(OrderInterface $order, $code, $description, $unitPrice, $quantity, $family, array $metadata = [])
    {
        $element = new Element();
        $element
            ->setCode($code)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setUnitPrice($unitPrice)
            ->setFamily($family)
            ->setMetadata($metadata)
        ;

        $order->addElement($element);
    }

    /**
     * @param array $childrens
     */
    private function normalizeChildrens(array &$childrens)
    {
        foreach ($childrens as $key => $children){
            if(is_numeric($children)){
                $childrens[$key] = $this->manager->find($children);
            }
        }
    }
}

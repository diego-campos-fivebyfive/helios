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

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\OrderInterface;
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

        foreach($project->getProjectModules() as $projectModule){

            /** @var \AppBundle\Entity\Component\ModuleInterface $module */
            $module = $projectModule->getModule();

            $this->addOrderElement($order,
                $module->getCode(),
                $module->getModel(),
                $projectModule->getUnitCostPrice(),
                $projectModule->getQuantity()
            );
        }

        foreach($project->groupInverters() as $groupInverter){

            /** @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter */
            $projectInverter = $groupInverter['projectInverter'];
            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $groupInverter['inverter'];
            $quantity = $groupInverter['quantity'];
            $price = $groupInverter['unitCostPrice'];

            $this->addOrderElement($order,
                $inverter->getCode(),
                $inverter->getModel(),
                $price,
                $quantity
            );
        }

        foreach($project->getProjectStringBoxes() as $projectStringBox){

            /** @var \AppBundle\Entity\Component\StringBoxInterface $stringBox */
            $stringBox = $projectStringBox->getStringBox();

            $this->addOrderElement($order,
                $stringBox->getCode(),
                $stringBox->getDescription(),
                $projectStringBox->getUnitCostPrice(),
                $projectStringBox->getQuantity()
            );
        }

        foreach($project->getProjectStructures() as $projectStructure){

            /** @var \AppBundle\Entity\Component\StructureInterface $structure */
            $structure = $projectStructure->getStructure();

            $this->addOrderElement($order,
                $structure->getCode(),
                $structure->getDescription(),
                $projectStructure->getUnitCostPrice(),
                $projectStructure->getQuantity()
            );
        }

        foreach($project->getProjectVarieties() as $projectVariety){

            /** @var \AppBundle\Entity\Component\VarietyInterface $variety */
            $variety = $projectVariety->getVariety();

            $this->addOrderElement($order,
                $variety->getCode(),
                $variety->getDescription(),
                $projectVariety->getUnitCostPrice(),
                $projectVariety->getQuantity()
            );
        }

        if(null != $member = $project->getMember()){
            if(null != $account = $member->getAccount()){
                $order->setAccount($account);
            }
        }

        $order->setDescription(sprintf('Sistema de %skWp', $project->getPower()));

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
            $order->addChildren($children);
            if(!$order->getAccount() && $children->getAccount()){
                $order->setAccount($children->getAccount());
            }
        }

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
    private function addOrderElement(OrderInterface $order, $code, $description, $unitPrice, $quantity)
    {
        $element = new Element();
        $element
            ->setCode($code)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setUnitPrice($unitPrice)
        ;

        $order->addElement($element);
    }

    /**
     * @param array $childrens
     */
    private function normalizeChildrens(array &$childrens)
    {
        foreach ($childrens as $key => $children){
            if(is_int($children)){
                $childrens[$key] = $this->manager->find($children);
            }
        }
    }
}
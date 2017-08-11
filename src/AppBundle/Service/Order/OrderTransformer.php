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
     * @return OrderInterface
     */
    public function transformFromProject(ProjectInterface $project)
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

        foreach($project->getProjectInverters() as $projectInverter){

            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();

            $this->addOrderElement($order,
                $inverter->getCode(),
                $inverter->getModel(),
                $projectInverter->getUnitCostPrice(),
                $projectInverter->getQuantity()
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

        $order->setAccount($project->getMember()->getAccount());

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
}
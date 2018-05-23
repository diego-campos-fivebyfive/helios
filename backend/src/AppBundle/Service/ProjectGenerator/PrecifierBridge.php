<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Component\Variety;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Service\Precifier\Calculator;
use AppBundle\Service\Precifier\MemorialHelper;
use AppBundle\Service\Precifier\RangeHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PrecifierBridge
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class PrecifierBridge
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PrecifierBridge constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ProjectInterface $project
     * @param Memorial|null $memorial
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function priceCost(ProjectInterface $project, Memorial $memorial = null)
    {
        if (!$project->getPower()) {
            $this->exception('Project power is null');
        }

        if (!$memorial) {
            /** @var MemorialHelper $memorialHelper */
            $memorialHelper = $this->container->get('precifier_memorial_helper');

            $memorial = $memorialHelper->load();
        }

        if ($memorial) {

            $defaults = $project->getDefaults();

            if (!isset($defaults['is_promotional'])) {
                $defaults['is_promotional'] = false;
            }

            $level = $defaults['is_promotional'] ? 'promotional' : $project->getLevel();

            $power = $project->getPower();

            $components = self::extractComponents($project);

            $componentsIds  = [];

            foreach ($components as $family => $elements) {
                $componentsIds[$family] = array_keys($elements);
            }

            /** @var RangeHelper $rangeHelper */
            $rangeHelper = $this->container->get('precifier_range_helper');

            $ranges = $rangeHelper->loadByComponentsIds($componentsIds);

            $costPrice = 0;

            $data = [
                'level' => $level,
                'power' => $power,
                'groups' => $components
            ];

            $groupsPrecified = Calculator::precify($data, $ranges);

            foreach ($groupsPrecified as $componentsPrecified) {
                foreach ($componentsPrecified as $data) {

                    if (!isset($data['price'])) {
                        /** @var \AppBundle\Entity\Component\ProjectElementInterface $element */
                        foreach ($data as $dataElement) {
                            $price = $dataElement['price'];

                            /** @var \AppBundle\Entity\Component\ProjectElementInterface $projectElement */
                            $projectElement = $dataElement['projectElement'];

                            $projectElement->setUnitCostPrice($price);

                            $costPrice += $projectElement->getUnitCostPrice();
                        }
                    } else {
                        $price = $data['price'];

                        /** @var \AppBundle\Entity\Component\ProjectElementInterface $projectElement */
                        $projectElement = $data['projectElement'];

                        $projectElement->setUnitCostPrice($price);

                        $costPrice += $projectElement->getUnitCostPrice();
                    }
                }
            }

            /** @var \AppBundle\Entity\Component\ProjectExtraInterface $projectExtra */
            foreach ($project->getProjectExtras() as $projectExtra){

                $unitPrice = (float) $projectExtra->getExtra()->getCostPrice();

                if (1 == $projectExtra->getExtra()->getPricingby()) {
                    $unitPrice = $unitPrice * $power;
                }

                $projectExtra->setUnitCostPrice($unitPrice);
                $costPrice += $unitPrice;
            }

            $project->setCostPrice($costPrice);
        }
    }

    public static function extractComponents(ProjectInterface $project)
    {
        $groups = [];

        /** @var ProjectInverter $projectInverter */
        foreach ($project->getProjectInverters() as $projectInverter) {

            /** @var Inverter $inverter */
            $inverter = $projectInverter->getInverter();

            $groups[ComponentInterface::FAMILY_INVERTER][$inverter->getId()][] = $projectInverter;
        }

        /** @var ProjectModule $projectModule */
        foreach ($project->getProjectModules() as $projectModule) {

            /** @var Module $module */
            $module = $projectModule->getModule();

            $groups[ComponentInterface::FAMILY_MODULE][$module->getId()] = $projectModule;
        }

        /** @var ProjectStructure $projectStructure */
        foreach ($project->getProjectStructures() as $projectStructure) {

            /** @var Structure $structure */
            $structure = $projectStructure->getStructure();

            $groups[ComponentInterface::FAMILY_STRUCTURE][$structure->getId()] = $projectStructure;
        }

        /** @var ProjectStringBox $projectStringBox */
        foreach ($project->getProjectStringBoxes() as $projectStringBox) {

            /** @var StringBox $stringBox */
            $stringBox = $projectStringBox->getStringBox();

            $groups[ComponentInterface::FAMILY_STRING_BOX][$stringBox->getId()] = $projectStringBox;
        }

        /** @var ProjectVariety $projectVariety */
        foreach ($project->getProjectVarieties() as $projectVariety) {

            /** @var Variety $variety */
            $variety = $projectVariety->getVariety();

            $groups[ComponentInterface::FAMILY_VARIETY][$variety->getId()] = $projectVariety;
        }

        if (null != $transformer = $project->getTransformer()) {
            $groups[ComponentInterface::FAMILY_VARIETY][$transformer->getVariety()->getId()] = $transformer;
        }

        return $groups;
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}

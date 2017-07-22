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

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Manager\Pricing\RangeManager;
use AppBundle\Manager\ProjectManager;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Model\KitPricing;

/**
 * ProjectPrecifier
 *
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */
class Precifier
{
    /**
     * @var RangeManager
     */
    private $manager;

    /**
     * Precifier constructor.
     * @param RangeManager $manager
     */
    public function __construct(RangeManager $manager)
    {
        $this->manager = $manager;
    }

    public function priceCost(ProjectInterface $project)
    {
        if(!$project->getPower())
            $this->exception('Project power is null');

        $memorial = $this->findMemorial();
        $components = $this->filterComponents($project);
        $codes = array_keys($components);
        $level = 'platinum';
        $ranges = $this->findRanges($codes, $level, $project->getPower());
        $taxPercent = 0.1;

        $costPrice = 0;

        /**
         * @var  $code
         * @var \AppBundle\Entity\Component\ProjectElementInterface $component
         */
        foreach ($components as $code => $component){
           /** @var Range $range */
            $range = $ranges[$code];

           $price = $range->getPrice() * (1 + $range->getMarkup()) / (1 - $taxPercent);

           $component->setUnitCostPrice($price);

           $costPrice += $price;
        }

        $project->setCostPrice($costPrice);
    }

    /**
     * @param ProjectInterface $project
     * @param \AppBundle\Entity\Component\PricingManager $pricingManager
     */
    public function priceSale(ProjectInterface $project, $pricingManager)
    {
        //$pricingManager = $this->get('app.kit_pricing_manager');
        $margins = $pricingManager->findAll();
        $percentEquipments = 0;
        $percentServices = 0;
        /** @var \AppBundle\Model\KitPricing $margin */
        foreach ($margins as $margin){
            switch ($margin->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $percentEquipments += $margin->percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $percentServices += $margin->percent;
                    break;
                default:
                    $percentServices += $margin->percent;
                    $percentEquipments += $margin->percent;
                    break;
            }
        }

        SalePrice::calculate($project, $percentEquipments, $percentServices);
    }

    /**
     * @param ProjectInterface $project
     * @return array
     */
    private function filterComponents(ProjectInterface $project)
    {
        $components=[];

        foreach ($project->getProjectInverters() as $projectInverter){
            $components[$projectInverter->getInverter()->getCode()] = $projectInverter;
        }

        foreach ($project->getProjectModules() as $projectModule){
            $components[$projectModule->getModule()->getCode()] = $projectModule;
        }

        foreach ($project->getProjectStructures() as $projectStructure){
            $components[$projectStructure->getStructure()->getCode()] = $projectStructure;
        }

        foreach ($project->getProjectStringBoxes() as $projectStringBox){
            $components[$projectStringBox->getStringBox()->getCode()] = $projectStringBox;
        }

        return $components;
    }

    /**
     * @param array $codes
     * @param $level
     * @param $power
     * @return array
     */
    private function findRanges(array $codes, $level, $power)
    {

        $qb = $this->manager->getEntityManager()->createQueryBuilder();
        $qb->select('r')->from(Range::class, 'r');
        $qb->where(
            $qb->expr()->in('r.code', $codes)
        );
        $qb->andwhere('r.level = :level');
        $qb->andWhere('r.initialPower <= :power');
        $qb->andWhere('r.finalPower >= :power');

        $qb->setParameters([
            'level' => $level,
            'power' => $power
        ]);

        $query = $qb->getQuery();

        /*$result = array_(function(Range $range){
            return $range->getCode();
        }, $query->getResult());*/

        $result = [];

        foreach ($query->getResult() as $range){
            $result[$range->getCode()] = $range;
        }
        return $result;
    }

    /**
     * @return Memorial
     */
    private function findMemorial()
    {
        return $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Memorial::class, 'm')
            ->where('m.id = :id')
            ->setParameters([
                'id'=> 87
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
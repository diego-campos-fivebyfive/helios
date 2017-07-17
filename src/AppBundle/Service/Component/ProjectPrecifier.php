<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Component;


use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Manager\ProjectManager;
use AppBundle\Entity\Pricing\Range;

/**
 * ProjectPrecifier
 *
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */
class ProjectPrecifier
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
    }

    public function priceCost(ProjectInterface $project)
    {
        $memorial = $this->findMemorial();

        $components = $this->filterComponents($project);

        //$codes = array_keys($components);
        $codes = [6475, 6473, 19419, 32365, 12946];

        $level = 'gold';
        $power = 200;

        $ranges = $this->findRanges($codes, $level, $power);

        $taxPercent = 0.1;

        /**
         * @var  $code
         * @var \AppBundle\Entity\Component\ProjectElementInterface $component
         */
        foreach ($components as $code => $component){
           /** @var Range $range */
            $range = $ranges[$code];

           $price = $range->getPrice() * (1 + $range->getMarkup()) / (1 - $taxPercent);

           $component->setUnitCostPrice($price);
        }

        $this->projectManager->save($project);
    }

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

        foreach ($project->getProjectStringBoxs() as $projectStringBox){
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

        $qb = $this->projectManager->getEntityManager()->createQueryBuilder();
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
    private function findMemorial(){
        return $this->projectManager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Memorial::class, 'm')
            ->where('m.id = :id')
            ->setParameters([
                'id'=> 2
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

}
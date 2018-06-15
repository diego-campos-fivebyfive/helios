<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Resolver;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Manager\ProjectManager;
use AppBundle\Manager\VarietyManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SolarEdgeResolver
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class SolarEdgeResolver
{
    const SOLAR_EDGE_EXPRESSION = "/solaredge/i";

    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SolarEdgeResolver constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->projectManager = $this->container->get('project_manager');
    }

    /**
     * @param ProjectInterface $project
     */
    public function resolve(ProjectInterface $project)
    {
        $projectInverters = $project->getProjectInverters();

        foreach ($projectInverters as $projectInverter) {
            /** @var Inverter $inverter */
            $inverter = $projectInverter->getInverter();

            $isSolarEdge = (bool) preg_match(self::SOLAR_EDGE_EXPRESSION, $inverter->getDescription());

            if ($isSolarEdge) {

                $this->optimize($project);

                break;
            }
        }
    }

    /**
     * @param ProjectInterface $project
     */
    private function optimize(ProjectInterface $project)
    {
        $varietyNumber = (int) round($project->countAssociatedModules() / 2);

        /** @var VarietyManager $varietyManager */
        $varietyManager = $this->container->get('variety_manager');

        $variety = $varietyManager->findOneBy([
            'type' => VarietyInterface::TYPE_OPTIMIZER,
            'subtype' => VarietyInterface::SUBTYPE_SOLAR_EDGE
        ]);

        $projectVariety = new ProjectVariety();
        $projectVariety
            ->setProject($project)
            ->setVariety($variety)
            ->setQuantity($varietyNumber)
        ;
    }
}

<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\ProjectInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Bridge
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Bridge constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        $level = $this->getLevel($defaults);
        $fdiMin = $defaults['fdi_min'];
        $fdiMax = $defaults['fdi_max'];
        $power = $defaults['power'];
        $phaseVoltage = $defaults['voltage'];
        $phaseNumber = $defaults['phases'];
        $inverterMakerId = $defaults['inverter_maker'];
        $stringBoxMakerId = $defaults['string_box_maker'];
    }

    /**
     * @param $defaults
     * @return string
     */
    private function getLevel($defaults)
    {
        if ($defaults['finame'] || $defaults['is_promotional']) {
            return $defaults['finame'] ? 'finame' : 'promotional';
        }

        return $defaults['level'];
    }
}
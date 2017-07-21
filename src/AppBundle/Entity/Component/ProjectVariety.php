<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjecVariety
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_variety")
 * @ORM\Entity
 */
class ProjectVariety implements ProjectVarietyInterface
{
    use ProjectElementTrait;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectStringBoxes")
     */
    private $project;

    /**
     * @var VarietyInterface
     *
     * @ORM\ManyToOne(targetEntity="Variety")
     */
    private $variety;

    /**
     * ProjectVariety constructor.
     */
    public function __construct()
    {
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectVariety($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @inheritDoc
     */
    public function getVariety()
    {
        return $this->variety;
    }

    /**
     * @inheritDoc
     */
    public function setVariety(VarietyInterface $variety)
    {
        $this->variety = $variety;

        return $this;
    }
}
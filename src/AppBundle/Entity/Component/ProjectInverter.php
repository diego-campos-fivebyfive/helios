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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectInverter
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_inverter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectInverter implements ProjectInverterInterface
{
    use ProjectElementTrait;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="loss", type="decimal", precision=10, scale=2)
     */
    private $loss;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectInverters")
     */
    private $project;

    /**
     * @var InverterInterface
     *
     * @ORM\ManyToOne(targetEntity="Inverter")
     */
    private $inverter;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProjectArea", mappedBy="projectInverter", cascade={"persist", "remove"})
     */
    private $projectAreas;

    /**
     * @var bool
     */
    private $operationIsChanged = false;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->loss          = 0;
        $this->operation     = 1;
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
        $this->projectAreas  = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectInverter($this);

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
    public function setInverter(InverterInterface $inverter)
    {
        $this->inverter = $inverter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverter()
    {
        return $this->inverter;
    }

    /**
     * @inheritDoc
     */
    public function setOperation($operation)
    {
        if($operation != $this->operation)
            $this->operationIsChanged = true;

        $this->operation = $operation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @inheritDoc
     */
    public function setLoss($loss)
    {
        $this->loss = $loss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLoss()
    {
        return $this->loss;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $power = 0;
        if (!$this->projectAreas->isEmpty()) {
            foreach ($this->projectAreas as $projectArea) {
                $power += $projectArea->getPower();
            }
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function operationIsChanged()
    {
        return $this->operationIsChanged;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        $metadata = [
            'nominal_power' => 0,
            'stc_power_max' => 0,
            'areas' => [],
            'fdi' => 0,
            'operable' => false,
            'level' => 'danger',
            'color' => 'red'
        ];

        if ($this->inverter) {

            $nominalPower = $this->inverter->getNominalPower();

            if ($this->operation) {

                $stcPowerMax = $this->getPower();

                if ($stcPowerMax) {

                    //$fdi = $nominalDcPower / $stcPowerMax;
                    $fdi = $stcPowerMax / $nominalPower;

                    $level = ($fdi >= .8 && $fdi <= 1.2) ? (($fdi >= .9 && $fdi <= 1.1) ? 'success' : 'warning') : 'danger';

                    $metadata['color'] = 'danger' == $level ? 'red' : ('warning' == $level ? 'yellow' : 'green');

                    $metadata['level'] = $level;
                    $metadata['fdi'] = $fdi;
                    $metadata['operable'] = 'danger' == $level ? false : true;

                    $metadata['stc_power_max'] = $stcPowerMax;

                    $metadata['areas'] = [];
                    foreach ($this->projectAreas as $projectArea) {
                        $metadata['areas'][] = $projectArea->getMetadata();
                    }
                }
            }

            $metadata['nominal_power'] = $nominalPower;

            return $metadata;
        }


        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function addProjectArea(ProjectAreaInterface $projectArea)
    {
        if(!$this->projectAreas->contains($projectArea)){
            $this->projectAreas->add($projectArea);

            if(!$projectArea->getProjectInverter()){
                $projectArea->setProjectInverter($this);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeProjectArea(ProjectAreaInterface $projectArea)
    {
        if($this->projectAreas->contains($projectArea)){
            $this->projectAreas->removeElement($projectArea);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectAreas()
    {
        return $this->projectAreas;
    }
}
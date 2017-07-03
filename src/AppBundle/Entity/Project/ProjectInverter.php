<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\Component\KitComponentInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectInverter
 *
 * @ORM\Table(name="app_project_inverter")
 * @ORM\Entity
 */
class ProjectInverter implements ProjectInverterInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="loss", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $loss;

    /**
     * @var json
     *
     * @ORM\Column(name="snapshot", type="json", nullable=true)
     */
    private $snapshot;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project\ProjectModule", mappedBy="inverter", cascade={"all"})
     */
    private $modules;

    /**
     * @var \AppBundle\Entity\Project\MpptOperation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project\MpptOperation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operation_id", referencedColumnName="id")
     * })
     */
    private $operation;

    /**
     * @var \AppBundle\Entity\Project\Project
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project\Project", inversedBy="inverters")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $project;

    /**
     * @var \AppBundle\Entity\Component\KitComponent
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\KitComponent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kit_inverter", referencedColumnName="id")
     * })
     */
    private $inverter;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->snapshot = [];
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string)$this->inverter;
    }

    function __clone()
    {
        $this->id = null;
        $this->project = null;
        $this->snapshot = null;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        /*if(!$project->getInverters()->contains($this)){
            $project->getInverters()->add($this);
        }*/

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
    public function setInverter(KitComponentInterface $inverter)
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
    public function setOperation(MpptOperationInterface $operation)
    {
        $this->operation = $operation;

        //$this->generateModules();

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
    public function addModule(ProjectModuleInterface $module)
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setInverter($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeModule(ProjectModuleInterface $module)
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            $module->setInverter(null);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModules()
    {
        return $this->modules;
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
        return (float)$this->loss;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $power = 0;
        if (!$this->modules->isEmpty()) {
            foreach ($this->modules as $projectModule) {
                $power += $projectModule->getPower();
            }
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataOperation()
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

            $inverterData = $this->inverter->toArray();

            $nominalPower = $inverterData['nominal_power'];

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
                    foreach ($this->modules as $projectModule) {
                        $metadata['areas'][] = $projectModule->getMetadataOperation();
                    }
                }
            }

            $metadata['nominal_power'] = $nominalPower;

            return $metadata;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        $errors = [];

        if (!$this->inverter)
            $errors[] = new ProjectError('project.error.undefined_inverter', $this);

        if (!$this->operation)
            $errors[] = new ProjectError('project.error.undefined_operation', $this);

        if (0 == $this->modules->count())
            $errors[] = new ProjectError('project.error.undefined_modules', $this);

        foreach ($this->modules as $projectModule) {
            $errors[] = $projectModule->getErrors();
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = [];
        $inverterData = $this->inverter->toArray();
        $operationData = $this->operation->toArray();

        foreach ($this->modules as $projectModule) {

            $moduleData = $projectModule->toArray();

            $data[] = [
                'inverter' => $inverterData,
                'module' => $moduleData,
                'operation' => $operationData,
            ];
        }

        return $data;
    }

    public function clearRelations()
    {
        if($this->inverter) {

            foreach($this->modules as $projectModule){
                if($projectModule instanceof ProjectModuleInterface){
                    $projectModule->clearRelations();
                }
            }

            /*
            $this->modules->forAll(function($key, ProjectModuleInterface $projectModule){
                $projectModule->clearRelations();
            });*/

            $this->snapshot['inverter'] = $this->inverter->getInverter()->snapshot();
            $this->inverter = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }
}


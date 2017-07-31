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
 * ProjectArea
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_area")
 * @ORM\Entity
 */
class ProjectArea implements ProjectAreaInterface
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
     * @var integer
     *
     * @ORM\Column(name="inclination", type="smallint")
     */
    private $inclination;

    /**
     * @var integer
     *
     * @ORM\Column(name="orientation", type="smallint")
     */
    private $orientation;

    /**
     * @var integer
     *
     * @ORM\Column(name="string_number", type="smallint")
     */
    private $stringNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="module_string", type="smallint")
     */
    private $moduleString;

    /**
     * @var string
     *
     * @ORM\Column(name="loss", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $loss;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var ProjectInverterInterface
     *
     * @ORM\ManyToOne(targetEntity="ProjectInverter", inversedBy="projectAreas")
     */
    private $projectInverter;

    /**
     * @var ProjectModuleInterface
     *
     * @ORM\ManyToOne(targetEntity="ProjectModule", inversedBy="projectAreas")
     */
    private $projectModule;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->inclination  = 0;
        $this->orientation  = 0;
        $this->stringNumber = 1;
        $this->moduleString = 1;
        $this->loss         = 0;
        $this->metadata     = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $inclination
     * @return ProjectArea
     */
    public function setInclination($inclination)
    {
        $this->inclination = $inclination;
        return $this;
    }

    /**
     * @return int
     */
    public function getInclination()
    {
        return $this->inclination;
    }

    /**
     * @param int $orientation
     * @return ProjectArea
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param int $stringNumber
     * @return ProjectArea
     */
    public function setStringNumber($stringNumber)
    {
        $this->stringNumber = $stringNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getStringNumber()
    {
        return $this->stringNumber;
    }

    /**
     * @param int $moduleString
     * @return ProjectArea
     */
    public function setModuleString($moduleString)
    {
        $this->moduleString = $moduleString;
        return $this;
    }

    /**
     * @return int
     */
    public function getModuleString()
    {
        return $this->moduleString;
    }

    /**
     * @param string $loss
     * @return ProjectArea
     */
    public function setLoss($loss)
    {
        $this->loss = $loss;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoss()
    {
        return $this->loss;
    }

    /**
     * @inheritDoc
     */
    public function countModules()
    {
        return $this->stringNumber * $this->moduleString;
    }

    /**
     * @inheritDoc
     */
    public function isConfigured()
    {
        $properties = [
            'moduleString',
            'stringNumber',
            'projectInverter',
            'projectModule'
        ];

        foreach ($properties as $property){
            if(!$this->$property) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        if($this->projectModule){
            return ($this->projectModule->getModule()->getMaxPower() * ($this->stringNumber * $this->moduleString)) / 1000;
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
        $area = 0;
        $projectModule = $this->getProjectModule();

        if($projectModule) {
            $power = $projectModule->getModule()->getMaxPower();
            $efficiency = $projectModule->getModule()->getEfficiency() * 1000;

            $area = (($this->stringNumber * $this->moduleString) * $power) / $efficiency;
        }

        return $area;
    }

    /**
     * @inheritDoc
     */
    public function getIndex()
    {
        $index = 0;
        foreach($this->projectInverter->getProjectAreas() as $projectArea){
            if($projectArea->getId() == $this->getId()){
                break;
            }else {
                $index++;
            }
        }

        return $index;
    }

    /**
     * @inheritDoc
     */
    public function getMpptName()
    {
        $index = $this->getIndex();

        $stack = explode(',', $this->generateMpptName());
        if(array_key_exists($index, $stack)){
            return $stack[$index];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setProjectInverter(ProjectInverterInterface $projectInverter)
    {
        $this->projectInverter = $projectInverter;

        $projectInverter->addProjectArea($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectInverter()
    {
        return $this->projectInverter;
    }

    /**
     * @inheritDoc
     */
    public function setProjectModule(ProjectModuleInterface $projectModule)
    {
        $this->projectModule = $projectModule;

        $projectModule->addProjectArea($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectModule()
    {
        return $this->projectModule;
    }

    /**
     * @return string
     */
    private function generateMpptName()
    {
        $operations = explode('.', $this->projectInverter->getOperation());
        $block = 'MPPT(%s)';
        $stack = [];
        $index = 1;
        foreach($operations as $operation){
            $content = $index;
            if($operation > 1){
                for($i = 2; $i <= $operation; $i++){
                    $content .= '+' . ($index+1);
                    $index++;
                }
            }
            $stack[] = sprintf($block, $content);
            $index++;
        }

        return implode(',', $stack);
    }

    /**
     * @inheritDoc
     */
    public function getMpptFactor()
    {
        $index = $this->getIndex();
        $operation = explode('.', $this->getProjectInverter()->getOperation());

        return (int) $operation[$index];
    }
}
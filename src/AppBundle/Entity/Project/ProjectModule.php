<?php

namespace AppBundle\Entity\Project;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Component\KitComponentInterface;

/**
 * ProjectModule
 *
 * @ORM\Table(name="app_project_module_legacy")
 * @ORM\Entity
 */
class ProjectModule implements ProjectModuleInterface
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
     * @var json
     *
     * @ORM\Column(name="metadata_operation", type="json", nullable=true)
     */
    private $metadataOperation;

    /**
     * @var json
     *
     * @ORM\Column(name="snapshot", type="json", nullable=true)
     */
    private $snapshot;

    /**
     * @var \AppBundle\Entity\Project\ProjectInverter
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project\ProjectInverter", inversedBy="modules")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_inverter", referencedColumnName="id")
     * })
     */
    private $inverter;

    /**
     * @var \AppBundle\Entity\Component\KitComponent
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\KitComponent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kit_module", referencedColumnName="id")
     * })
     */
    private $module;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->stringNumber = 1;
        $this->moduleString = 1;
        $this->inclination  = 0;
        $this->orientation  = 0;
        $this->metadataOperation = [];
        $this->snapshot = [];
    }

    function __clone()
    {
        $this->id = null;
        $this->snapshot = null;
        $this->inverter = null;
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
    public function setInverter(ProjectInverterInterface $inverter = null)
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
    public function setModule(KitComponentInterface $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    public function setInclination($inclination)
    {
        $this->inclination = (int) $inclination;

        if($this->inclination < self::INCLINATION_MIN || $this->inclination > self::INCLINATION_MAX)
            $this->outOfRangeException();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInclination()
    {
        return $this->inclination;
    }

    /**
     * @inheritDoc
     */
    public function setOrientation($orientation)
    {
        $this->orientation = (int) $orientation;

        if($this->orientation < self::ORIENTATION_MIN || $this->orientation > self::ORIENTATION_MAX)
            $this->outOfRangeException();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @inheritDoc
     */
    public function setStringNumber($stringNumber)
    {
        $this->stringNumber = $stringNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStringNumber()
    {
        return $this->stringNumber;
    }

    /**
     * @inheritDoc
     */
    public function setModuleString($moduleString)
    {
        $this->moduleString = $moduleString;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModuleString()
    {
        return $this->moduleString;
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
    public function countModules()
    {
        return  $this->moduleString * $this->stringNumber;
    }

    /**
     * @inheritDoc
     */
    public function getLoss()
    {
        return (float) $this->loss;
    }

    /**
     * @inheritDoc
     */
    public function getUnitPriceSale()
    {
        if(null != $price = $this->getModule()->getPrice()){
            return $this->getModule()->getUnitPriceSale();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSale()
    {
        $quantity = $this->quantity;

        return $this->getUnitPriceSale() * $quantity;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $basePower = 0;
        if($this->module){
            $basePower = $this->module->getModule()->getMaxPower();
            //return ($this->module->getModule()->getMaxPower() * $this->countModules()) / 1000;
        }elseif (!empty($this->snapshot) && array_key_exists('module', $this->snapshot)){
            $basePower = $this->snapshot['module']['maxPower'];
        }

        return $basePower * $this->countModules() / 1000;
    }

    /**
     * @inheritDoc
     */
    public function getTotalArea()
    {
        $area = 0;

        if($this->module->getModule()) {
            $power = $this->module->getModule()->getMaxPower();
            $efficiency = $this->module->getModule()->getEfficiency() * 1000;

            $area = ($this->countModules() * $power) / $efficiency;
        }

        return $area;
    }

    /**
     * @inheritDoc
     */
    public function setMetadataOperation(array $metadata)
    {
        $this->metadataOperation = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataOperation()
    {
        return $this->metadataOperation;
    }

    /**
     * @inheritDoc
     */
    public function getIndex()
    {
        $index = 0;
        foreach($this->inverter->getModules() as $module){
            if($module->getId() == $this->getId()){
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
    public function getMpptFactor()
    {
        $index = $this->getIndex();
        $operation = $this->inverter->getOperation()->getOperation();

        return $operation[$index];
    }

    /**
     * @return string
     */
    public function getMpptName()
    {
        return $this->inverter->getOperation()->getName($this->getIndex());
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        $errors = [];
        if(!$this->inverter)
            $errors[] = new ProjectError('project.error.undefined_inverter', $this);

        if(!$this->module)
            $errors[] = new ProjectError('project.error.undefined_module', $this);

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function isComputable()
    {
        return empty($this->getErrors());
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = $this->module ? $this->module->toArray() : [];

        $data['inclination'] = $this->inclination;
        $data['orientation'] = $this->orientation;
        $data['string_number'] = $this->stringNumber;
        $data['module_per_string'] = $this->moduleString;
        $data['total_modules'] = $this->countModules();
        $data['total_area'] = round($this->getTotalArea(), 2);
        $data['total_power'] = $this->getPower();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    public function clearRelations()
    {
        if($this->module) {
            $this->snapshot['module'] = $this->module->getModule()->snapshot();
            $this->module = null;
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function outOfRangeException()
    {
        throw new \InvalidArgumentException(self::ERROR_OUT_OF_RANGE);
    }

    /**
     * @inheritDoc
     */
    public static function getRange($key = null)
    {
        $range = [
            'inclination' => ['min' => self::INCLINATION_MIN,  'max' => self::INCLINATION_MAX ],
            'orientation' => ['min' => self::ORIENTATION_MIN,  'max' => self::ORIENTATION_MAX ]
        ];

        if($key){
            $type = $key;
            $index = null;

            if(strpos($key, '.') > 0) {
                list($type, $index) = explode('.', $key);
            }

            if(array_key_exists($type, $range)){
                $range = $range[$type];

                if($index && array_key_exists($index, $range)){
                    $range = $range[$index];
                }
            }
        }

        return $range;
    }
}


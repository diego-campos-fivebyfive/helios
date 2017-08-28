<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="app_project_tax")
 * @ORM\Entity
 */
class ProjectTax implements ProjectTaxInterface
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=10)
     */
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=15)
     */
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $value;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectTaxes", cascade={"persist"})
     */
    private $project;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectTax($this);

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
     * @inheritdoc
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @inheritdoc
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @inheritdoc
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return (float) $this->value;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        $amount = $this->isAbsolute()
            ? ($this->isDiscount() && $this->value > 0 ? $this->value * (-1) : $this->value)
            : ($this->getTargetPrice() * $this->getPercent()) ;

        return (float) $amount;
    }

    /**
     * @inheritDoc
     */
    public function getAmountFormatted($symbol = 'R$')
    {
        return sprintf('%s %s', $symbol, number_format($this->getAmount(), 2, ',', '.'));
    }

    /**
     * @inheritdoc
     */
    public function getPercent($decimal = true)
    {
        $target = $this->getTargetPrice();

        if($target) {

            $percent = $this->isAbsolute() ? (($this->value * 100) / $target) / 100 : ($this->value / 100);

            $percent = $this->isDiscount() && $percent > 0 ? $percent * (-1) : $percent;

            return $decimal ? $percent : $percent * 100;
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public static function getTaxOperations()
    {
        return [
            self::OPERATION_DISCOUNT => self::OPERATION_DISCOUNT,
            self::OPERATION_ADDITION => self::OPERATION_ADDITION
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getTaxTargets()
    {
        return [
            self::TARGET_GENERAL => self::TARGET_GENERAL,
            self::TARGET_EQUIPMENTS => self::TARGET_EQUIPMENTS,
            self::TARGET_SERVICES => self::TARGET_SERVICES
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getTaxTypes()
    {
        return [
            self::TYPE_ABSOLUTE => self::TYPE_ABSOLUTE,
            self::TYPE_PERCENT => self::TYPE_PERCENT
        ];
    }

    /**
     * @inheritDoc
     */
    public static function currency($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * @return float|int
     */
    private function getTargetPrice()
    {
        switch ($this->target){
            case self::TARGET_EQUIPMENTS:
                return $this->project->getSalePriceEquipments();
                break;
            case self::TARGET_SERVICES:
                return $this->project->getSalePriceServices();
                break;
            default:
                return $this->project->getSalePriceServices() + $this->project->getSalePriceEquipments();
                break;
        }
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return self::TYPE_ABSOLUTE == $this->type;
    }

    /**
     * @return bool
     */
    public function isDiscount()
    {
        return self::OPERATION_DISCOUNT == $this->operation;
    }

    /**
     * @return bool
     */
    public function isTargetServices()
    {
        return self::TARGET_SERVICES == $this->target;
    }

    /**
     * @return bool
     */
    public function isTargetEquipments()
    {
        return self::TARGET_EQUIPMENTS == $this->target;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'operation' => $this->getOperation(),
            'target' => $this->getTarget(),
            'type' => $this->getType(),
            'amount' => $this->getAmount(),
            'percent' => round($this->getPercent(), 2)
        ];
    }

    /**
     * @inheritDoc
     */
    public function toJson()
    {
        if(false != $json = json_encode($this->toArray())){
            return $json;
        }

        throw new \Exception(json_last_error_msg());
    }
}


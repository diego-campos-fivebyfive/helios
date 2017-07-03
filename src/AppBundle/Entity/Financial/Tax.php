<?php

namespace AppBundle\Entity\Financial;

use AppBundle\Entity\TokenizerTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tax
 *
 * @ORM\Table(name="app_financial_tax")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Tax implements TaxInterface
{
    use TokenizerTrait;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Financial\ProjectFinancial
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Financial\ProjectFinancial", inversedBy="taxes", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="financial_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $financial;

    /**
     * @inheritDoc
     */
    function __construct(ProjectFinancialInterface $financial)
    {
        $financial->addTax($this);
        $this->financial = $financial;
    }

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
    public function getFinancial()
    {
        return $this->financial;
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
        return $this->isTargetService()
            ? $this->financial->getPriceServices()
            : ($this->isTargetEquipments() ? $this->financial->getPriceEquipments() : $this->financial->getPriceOriginal() );
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
     * @inheritDoc
     */
    public function isTargetService()
    {
        return $this->isTargetServices();
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
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
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


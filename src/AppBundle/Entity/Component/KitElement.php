<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * KitElement
 *
 * @ORM\Table(name="app_kit_element")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class KitElement implements KitElementInterface
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", length=1)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price", type="decimal", precision=10, scale=2)
     */
    private $unitPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="price_strategy", type="smallint", length=1)
     */
    private $priceStrategy;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="smallint")
     */
    private $rate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Component\Kit
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\Kit", inversedBy="elements")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kit", referencedColumnName="id", nullable=false)
     * })
     */
    private $kit;

    function __construct()
    {
        $this->rate      = 100;
        $this->unitPrice = 0;
        $this->quantity  = 1;
        $this->priceStrategy = self::PRICE_STRATEGY_ABSOLUTE;
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
     * TODO: Add TokenizerTrait Here
     */
    public function getToken()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setKit(KitInterface $kit)
    {
        $this->kit = $kit;
        if(!$kit->getElements()->contains($this)){
            $kit->getElements()->add($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getKit()
    {
        return $this->kit;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        if(!array_key_exists($type, self::getTypes()))
            $this->unsupportedDefinitionException('type', $type);

        if($type == self::TYPE_SERVICE)
            $this->quantity = 1;

        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
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
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUnitPrice()
    {
        return $this->isPrecificable() ? (float) $this->unitPrice : null ;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        if(!$this->kit && $this->priceStrategy != self::PRICE_STRATEGY_ABSOLUTE)
            throw new \InvalidArgumentException(self::ERROR_KIT_UNDEFINED);

        switch ($this->priceStrategy){
            case self::PRICE_STRATEGY_PERCENTAGE:
                $price = $this->kit->getFinalCost() * ($this->rate / 100);
                break;

            case self::PRICE_STRATEGY_INCREMENTAL:
                $price = $this->kit->countModules() * $this->unitPrice;
                break;

            default:
            case self::PRICE_STRATEGY_ABSOLUTE:
                $price = $this->quantity * $this->unitPrice;
                break;
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPrice()
    {
        return $this->isPrecificable() ? $this->getPrice() : null ;
    }

    /**
     * @inheritDoc
     */
    public function getUnitPriceSale()
    {
        if(null != $price = $this->getUnitPrice()){

            $cost = $this->isElement() ? $this->kit->getCostOfEquipments() : $this->getKitCostServices();
            $sale = $this->isElement() ? $this->kit->getPriceSaleEquipments() : $this->kit->getPriceSaleServices();
            $percent = $price / $cost;
            $unitPriceSale = $percent * $sale;

            return round($unitPriceSale, 2);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceSale()
    {
        if(null != $priceSale = $this->getUnitPriceSale()){
            return $priceSale * $this->getQuantity();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        if($this->isService())
            $quantity = 1;

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return self::PRICE_STRATEGY_ABSOLUTE == $this->priceStrategy ? $this->quantity : $this->kit->countModules();
    }

    /**
     * @inheritDoc
     */
    public function setPriceStrategy($priceStrategy)
    {
        if(!array_key_exists($priceStrategy, self::getPriceStrategies()))
            $this->unsupportedDefinitionException('priceStrategy', $priceStrategy);

        $this->priceStrategy = $priceStrategy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriceStrategy()
    {
        return $this->priceStrategy;
    }

    /**
     * @inheritDoc
     */
    public function setRate($rate)
    {
        $this->rate = (int) $rate;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @inheritDoc
     */
    public function isElement()
    {
        return $this->type == self::TYPE_ELEMENT;
    }

    /**
     * @inheritDoc
     */
    public function isService()
    {
        return $this->type == self::TYPE_SERVICE;
    }

    /**
     * @inheritDoc
     */
    public function isIncremental()
    {
        return $this->priceStrategy == self::PRICE_STRATEGY_INCREMENTAL;
    }

    /**
     * @inheritDoc
     */
    public function isPrecificable()
    {
        return $this->isService() || $this->kit->isPriceComputable();
    }

    /**
     * @inheritDoc
     */
    public static function getTypes()
    {
        return [
            self::TYPE_ELEMENT => 'type_element',
            self::TYPE_SERVICE => 'type_service'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getPriceStrategies()
    {
        return [
            self::PRICE_STRATEGY_ABSOLUTE => 'price_strategy_absolute',
            self::PRICE_STRATEGY_INCREMENTAL => 'price_strategy_incremental',
            //self::PRICE_STRATEGY_PERCENTAGE => 'price_strategy_percentage'
        ];
    }

    /**
     * @return float
     */
    private function getKitCostEquipments()
    {
        return $this->kit->getFinalCost();
    }

    /**
     * @return float
     */
    private function getKitCostServices()
    {
        return $this->kit->getTotalPriceServices();
    }

    /**
     * @param $property
     * @param $value
     */
    private function unsupportedDefinitionException($property, $value)
    {
        throw new \InvalidArgumentException(sprintf(self::ERROR_UNSUPPORTED_DEFINITION, $property, $value));
    }
}


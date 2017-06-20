<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\TokenizerTrait;
use AppBundle\Model\Snapshot;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Kit
 *
 * @ORM\Table(name="app_kit")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Kit implements KitInterface
{
    use TokenizerTrait;
    use PricingCalculatorTrait;
    use Snapshot;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $invoiceBasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_base_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $deliveryBasePrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoice_price_strategy", type="smallint", length=1)
     */
    private $invoicePriceStrategy;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivery_price_strategy", type="smallint", length=1)
     */
    private $deliveryPriceStrategy;

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
     * @var json
     *
     * @ORM\Column(name="attributes", type="json")
     */
    private $attributes;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Component\KitComponent", mappedBy="kit", cascade={"persist","remove"})
     */
    private $components;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Component\KitElement", mappedBy="kit", cascade={"persist","remove"})
     */
    private $elements;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="kits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account", referencedColumnName="id", nullable=false)
     * })
     */
    private $account;

    function __construct()
    {
        $this->components = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->invoicePriceStrategy = self::PRICE_STRATEGY_ABS;
        $this->deliveryPriceStrategy = self::PRICE_STRATEGY_ABS;
        $this->invoiceBasePrice = 0;
        $this->deliveryBasePrice = 0;
        $this->pricingTaxes = [];
        $this->attributes = [];
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
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
    public function getNumber()
    {
        return $this->getAttribute('index');
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
    public function setIdentifier($identifier)
    {
        $this->identifier = substr($identifier, 0, 30);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $index = $this->getAttribute('index');
        $power = number_format($this->getPower() / 1000, 2, ',', '') . 'kWp';

        /*$makerName = '';

        if($this->getInverters()->first()){
            if(null != $maker = $this->getInverters()->first()->getMaker()){
                if($maker instanceof MakerInterface)
                    $makerName = substr($maker->getName(), 0, 10);
            }
        }*/

        return sprintf('%04d - %s - %smod - %sinv%s%s',
            $index,
            $power,
            $this->countModules(),
            $this->countInverters(),
            $this->identifier ? ' -- ' : '',
            $this->identifier
        );
    }

    /**
     * @inheritDoc
     */
    public function setAccount(BusinessInterface $account)
    {
        if(!$account->isAccount())
            throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_CONTEXT);

        $this->account = $account;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function addComponent(KitComponentInterface $component)
    {
        if(!$this->components->contains($component)){
            $this->components->add($component);
            $component->setKit($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeComponent(KitComponentInterface $component)
    {
        if($this->components->contains($component)){
            $this->components->removeElement($component);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @inheritDoc
     */
    public function addModule(KitComponentInterface $module)
    {
        return $this->addComponent($module);
    }

    /**
     * @inheritDoc
     */
    public function removeModule(KitComponentInterface $module)
    {
        return $this->removeComponent($module);
    }

    /**
     * @inheritDoc
     * @return ArrayCollection
     */
    public function getModules()
    {
        return $this->components->filter(function(KitComponentInterface $component){
            return $component->isModule();
        });
    }

    /**
     * @inheritDoc
     */
    public function containsModule(ModuleInterface $module)
    {
        return $this->filterComponentModule($module) instanceof KitComponentInterface;
    }

    /**
     * @inheritDoc
     */
    public function filterComponentModule(ModuleInterface $module)
    {
        return $this->getModules()->filter(function(KitComponentInterface $component) use($module){
            return $component->getModule()->getId() === $module->getId();
        })->first();
    }

    /**
     * @inheritDoc
     */
    public function addInverter(KitComponentInterface $inverter)
    {
        return $this->addComponent($inverter);
    }

    /**
     * @inheritDoc
     */
    public function removeInverter(KitComponentInterface $inverter)
    {
        return $this->removeComponent($inverter);
    }

    /**
     * @inheritDoc
     * @@return ArrayCollection
     */
    public function getInverters()
    {
        return $this->components->filter(function(KitComponentInterface $component){
            return $component->isInverter();
        });
    }

    /**
     * @inheritDoc
     */
    public function containsInverter(InverterInterface $inverter)
    {
        return $this->filterComponentInverter($inverter) instanceof KitComponentInterface;
    }

    /**
     * @inheritDoc
     */
    public function filterComponentInverter(InverterInterface $inverter)
    {
        return $this->getInverters()->filter(function(KitComponentInterface $component) use($inverter){
            return $component->getInverter()->getId() === $inverter->getId();
        })->first();
    }

    /**
     * @inheritDoc
     */
    public function filterComponent(ComponentInterface $component)
    {
        return $component instanceof InverterInterface
            ? $this->filterComponentInverter($component)
            : $this->filterComponentModule($component) ;
    }

    /**
     * @inheritDoc
     */
    public function addElement(KitElementInterface $element)
    {
        if(!$this->elements->contains($element)){
            $this->elements->add($element);
            $element->setKit($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeElement(KitElementInterface $element)
    {
        if($this->elements->contains($element)){
            $this->elements->removeElement($element);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     * @return ArrayCollection
     */
    public function getElementItems()
    {
        return $this->elements->filter(function(KitElementInterface $element){
            return $element->isElement();
        });
    }

    /**
     * @inheritDoc
     * @return ArrayCollection
     */
    public function getElementServices()
    {
        return $this->elements->filter(function(KitElementInterface $element){
            return $element->isService();
        });
    }

    /**
     * @inheritDoc
     */
    public function hasItems()
    {
        return $this->getElementItems()->count() > 0;
    }

    /**
     * @inheritDoc
     */
    public function hasServices()
    {
        return $this->getElementServices()->count() > 0;
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceBasePrice($basePrice)
    {
        $this->invoiceBasePrice = $basePrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceBasePrice()
    {
        return (float) $this->invoiceBasePrice;
    }

    /**
     * @inheritDoc
     */
    public function getInvoicePrice()
    {
        return $this->getCostOfEquipments();
    }

    /**
     * @inheritDoc
     */
    public function getCostOfEquipments()
    {
        if($this->invoicePriceStrategy == self::PRICE_STRATEGY_ABS)
            return round($this->invoiceBasePrice, 2);

        return round($this->getTotalPriceComponents() + $this->getTotalPriceElements(), 2);
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryBasePrice($basePrice)
    {
        $this->deliveryBasePrice = $basePrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryBasePrice()
    {
        return $this->deliveryBasePrice;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryPrice()
    {
        if($this->deliveryPriceStrategy == self::PRICE_STRATEGY_ABS)
            return round($this->deliveryBasePrice, 2);

        return round($this->getCostOfEquipments() * ($this->deliveryBasePrice / 100), 2);
    }

    /**
     * @inheritDoc
     */
    public function setInvoicePriceStrategy($strategy)
    {
        if(!array_key_exists($strategy, self::getInvoicePriceStrategies()))
            $this->unsupportedPriceStrategyException();

        $this->invoicePriceStrategy = $strategy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInvoicePriceStrategy()
    {
        return $this->invoicePriceStrategy;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryPriceStrategy($strategy)
    {
        if(!array_key_exists($strategy, self::getDeliveryPriceStrategies()))
            $this->unsupportedPriceStrategyException();

        $this->deliveryPriceStrategy = $strategy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryPriceStrategy()
    {
        return $this->deliveryPriceStrategy;
    }

    /**
     * @inheritDoc
     * @deprecated Use getTotalPriceComponents
     */
    public function getMandatoryPrice()
    {
        return $this->getTotalPriceComponents();
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceOptionals()
    {
        return $this->getTotalPriceElements() + $this->getTotalPriceServices();
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceElements()
    {
        $price = 0;
        foreach($this->elements as $element){
            if($element instanceof KitElementInterface)
                if($element->isElement())
                    $price += $element->getTotalPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceServices()
    {
        $price = 0;
        foreach($this->elements as $element){
            if($element instanceof KitElementInterface)
                if($element->isService())
                    $price += $element->getTotalPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceComponents()
    {
        $price = 0;
        foreach($this->components as $component){
            $price += $component->getTotalPrice();
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPrice()
    {
        return round($this->getFinalCost() + $this->getTotalPriceServices(), 2);
    }

    /**
     * @inheritDoc
     */
    public function getFinalCost()
    {
        return round($this->getInvoicePrice() + $this->getDeliveryPrice(), 2);
    }

    /**
     * @inheritDoc
     */
    public function countModules()
    {
        $count = 0;
        foreach($this->components as $component){
            if($component->isModule())
                $count += $component->getQuantity();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function countInverters()
    {
        $count = 0;
        foreach($this->components as $component){
            if($component->isInverter())
                $count += $component->getQuantity();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataInverters()
    {
        $metadata = [];
        foreach($this->getInverters() as $inverter){

            if(!array_key_exists($inverter->getId(), $metadata)){
                $metadata[$inverter->getId()] = [];
            }

            $data = [
                'id' => $inverter->getInverter()->getId(),
                'serial' => $inverter->getInverter()->getSerial(),
                'model' => $inverter->getInverter()->getModel(),
                'quantity' => $inverter->getQuantity()
            ];

            $metadata[$inverter->getId()] = $data;
        }

        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataModules()
    {
        $metadata = [];
        foreach($this->getModules() as $module){

            if(!array_key_exists($module->getId(), $metadata)){
                $metadata[$module->getId()] = [];
            }

            $data = [
                'id' => $module->getModule()->getId(),
                'serial' => $module->getModule()->getSerial(),
                'model' => $module->getModule()->getModel(),
                'quantity' => $module->getQuantity()
            ];

            $metadata[$module->getId()] = $data;
        }

        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = [
            'id' => $this->id,
            'token' => $this->token,
            'invoice_price' => $this->getInvoicePrice(),
            'delivery_price' => $this->getDeliveryPrice(),
            'inverters' => []
        ];

        foreach($this->getInverters() as $inverter){
            $data['inverters'][] = $inverter->toArray();
        }

        foreach($this->getModules() as $module){
            $data['modules'][] = $module->toArray();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public static function getInvoicePriceStrategies()
    {
        return [
            self::PRICE_STRATEGY_ABS => 'price_abs',
            self::PRICE_STRATEGY_SUM => 'price_sum'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDeliveryPriceStrategies()
    {
        return [
            self::PRICE_STRATEGY_ABS => 'price_abs_delivery',
            self::PRICE_STRATEGY_PCT => 'price_pct_delivery'
        ];
    }

    /**
     * @inheritDoc
     */
    public function setPricingTaxes(array $pricingTaxes)
    {
        $this->pricingTaxes = $pricingTaxes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPricingTaxes()
    {
        return $this->pricingTaxes;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $power = 0;
        foreach($this->getModules() as $kitModule){
            $power += $kitModule->getPower();
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function isPriceComputable()
    {
        return self::PRICE_STRATEGY_SUM == $this->invoicePriceStrategy;
    }

    /**
     * @inheritDoc
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAttribute($key)
    {
        if($this->hasAttribute($key)){
            unset($this->attributes[$key]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key, $default = null)
    {
        if($this->hasAttribute($key)){
            return $this->attributes[$key];
        }
        return $default;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable()
    {
        $inverters = $this->countInverters();
        $modules = $this->countModules();

        return ($inverters > 0 && $modules > 0 && $modules >= $inverters);
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

    private function unsupportedPriceStrategyException()
    {
        throw new \InvalidArgumentException(self::ERROR_PRICE_STRATEGY);
    }
}


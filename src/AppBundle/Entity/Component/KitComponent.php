<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * KitComponent
 *
 * @ORM\Table(name="app_kit_component")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class KitComponent implements KitComponentInterface
{
    use KitComponentTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\Kit", inversedBy="components")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kit_id", referencedColumnName="id")
     * })
     */
    private $kit;

    /**
     * @var \AppBundle\Entity\Component\Inverter
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\Inverter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inverter_id", referencedColumnName="id")
     * })
     */
    private $inverter;

    /**
     * @var \AppBundle\Entity\Component\Module
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\Module")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="module_id", referencedColumnName="id")
     * })
     */
    private $module;

    /**
     * @var string|MakerInterface
     */
    private $maker;

    /**
     * @var string
     */
    private $serial;

    /**
     * KitComponent constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->price = 0;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->getComponent();
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
    public function setMaker(MakerInterface $maker)
    {
        $this->maker = $maker;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaker()
    {
        if(null != $component = $this->getComponent()){
            $this->maker = $component->getMaker();
        }

        return $this->maker;
    }

    /**
     * @inheritDoc
     */
    public function setSerial(ComponentInterface $serial = null)
    {
        $this->serial = $serial;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSerial()
    {
        /*if(null != $component = $this->getComponent()){
            $this->serial = $component;
        }*/

        return $this->serial;
    }

    /**
     * @inheritDoc
     */
    public function setKit(KitInterface $kit)
    {
        $this->kit = $kit;

        if(!$kit->getComponents()->contains($this)){
            $kit->getComponents()->add($this);
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
    public function setComponent(ComponentInterface $component)
    {
        if($component instanceof InverterInterface) {
            $this->setInverter($component);
        }else{
            $this->setModule($component);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getComponent()
    {
        return $this->isInverter() ? $this->inverter : $this->module;
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
    public function setModule(ModuleInterface $module)
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
    public function setQuantity($quantity)
    {
        if((int) $quantity < 1) $quantity = 1;

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        if(!$this->kit->isPriceComputable())
            return null;

        return (float) $this->price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPrice()
    {
        if(!$this->kit->isPriceComputable())
            return null;

        return $this->getQuantity() * $this->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getUnitPriceSale()
    {
        if(null != $price = $this->getPrice()){

            $costEquipments = $this->kit->getCostOfEquipments();
            $saleEquipments = $this->kit->getPriceSaleEquipments();
            $percent = $price / $costEquipments;
            $unitPriceSale = $percent * $saleEquipments;

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
    public function getFullPower()
    {
        return $this->getPower();
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        //TODO: Check if inverters allow this method
        if($this->isModule()) {
            return $this->getModule()->getMaxPower() * $this->getQuantity();
        }

        return $this->getInverter()->getNominalPower() * $this->getQuantity();
    }

    /**
     * @inheritDoc
     */
    public function usePriceComponent()
    {
        throw new \BadMethodCallException();
        /*if(null != $component = $this->getComponent()){
            //$this->price = $this->getComponent()->getPrice();
        }*/
    }

    /**
     * @inheritDoc
     */
    public function isInverter()
    {
        return $this->inverter instanceof InverterInterface;
    }

    /**
     * @inheritDoc
     */
    public function isModule()
    {
        return $this->module instanceof ModuleInterface;
    }

    /**
     * @inheritDoc
     */
    public function makeHelpers()
    {
        if(null != $component = $this->getComponent()){
            $this
                ->setSerial($component)
                ->setMaker($component->getMaker());
        }
    }

    /**
     * @return float
     */
    private function getKitCostEquipments()
    {
        return $this->kit->getFinalCost();
    }
}


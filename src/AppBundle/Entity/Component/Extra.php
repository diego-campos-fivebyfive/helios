<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Item
 *
 * @ORM\Table(name="app_extra")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Extra implements ExtraInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="pricingBy", type="integer")
     */
    private $pricingBy;

    /**
     * @var string
     *
     * @ORM\Column(name="costPrice", type="decimal", precision=10, scale=2)
     */
    private $costPrice;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->type = self::TYPE_PRODUCT;
        $this->pricingBy = self::PRICING_FIXED;
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
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
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
    public function setPricingBy($pricingBy)
    {
        $this->pricingBy = $pricingBy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPricingBy()
    {
        return $this->pricingBy;
    }

    /**
     * @inheritDoc
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * @inheritDoc
     */
    public function isService()
    {
        return self::TYPE_SERVICE == $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isProduct()
    {
        return self::TYPE_PRODUCT == $this->type;
    }

    /**
     * @inheritDoc
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PRODUCT => 'Produto',
            self::TYPE_SERVICE => 'Serviço'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getPricingOptions()
    {
        return [
            self::PRICING_FIXED => 'Fixo',
            self::PRICING_POWER => 'Por Potência'
        ];
    }
}


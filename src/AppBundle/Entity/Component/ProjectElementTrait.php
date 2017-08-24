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

use AppBundle\Entity\Pricing\RangeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProjectElementTrait
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
trait ProjectElementTrait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(type="object", nullable=true)
     */
    private $markup;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_cost_price", type="float")
     */
    private $unitCostPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_sale_price", type="float", nullable=true)
     */
    private $unitSalePrice;

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
    public function getCode()
    {
        $source = array_reverse(explode('\\', get_class($this)));

        $method = str_ireplace('Project', 'get', $source[0]);

        return $this->$method()->getCode();
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;

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
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @inheritDoc
     */
    public function setUnitCostPrice($unitCostPrice)
    {
        $this->unitCostPrice = $unitCostPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUnitCostPrice()
    {
        return (float) $this->unitCostPrice;
    }

    /**
     * @inheritDoc
     */
    public function setUnitSalePrice($unitSalePrice)
    {
        $this->unitSalePrice = $unitSalePrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUnitSalePrice()
    {
        return (float) $this->unitSalePrice;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCostPrice()
    {
        return $this->unitCostPrice * $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function getTotalSalePrice()
    {
        return $this->unitSalePrice * $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function applyRange(RangeInterface $range)
    {
        if($range->getCode() != $this->getCode())
            throw new \InvalidArgumentException('Incompatible codes');

        $this->unitCostPrice = $range->getPrice();

        return $this;
    }
}
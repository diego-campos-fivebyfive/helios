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
     * @ORM\Column(name="unit_cost_price", type="decimal", precision=10, scale=2)
     */
    private $unitCostPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_sale_price", type="decimal", precision=10, scale=2)
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
    public function setQuantity($quantity)
    {
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
}
<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;

/**
 * Class Operation
 * @package AppBundle\Service\Stock
 */
class Operation
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $description;

    /**
     * Operation constructor.
     * @param ProductInterface $product
     * @param $amount
     * @param $description
     */
    function __construct(ProductInterface $product, $amount, $description)
    {
        if(!is_int($amount)) throw new \InvalidArgumentException('Invalid amount value type');

        $this->product = $product;
        $this->amount = $amount;
        $this->description = $description;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param ProductInterface $product
     * @param $amount
     * @param $description
     * @return Operation
     */
    public static function create(ProductInterface $product, $amount, $description)
    {
        return new self($product, $amount, $description);
    }
}

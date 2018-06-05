<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Stock;

/**
 * Class Component
 * This class generates a operation pattern for stock control
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Operation
{
    /**
     * @var string
     */
    private $family;

    /**
     * @var int
     */
    private $identity;

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
     * @param $family
     * @param $identity
     * @param $amount
     * @param $description
     */
    function __construct($family, $identity, $amount, $description)
    {
        if(!is_int($amount)) throw new \InvalidArgumentException('Invalid amount value type');

        $this->family = $family;
        $this->identity = $identity;
        $this->amount = $amount;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @return int
     */
    public function getIdentity()
    {
        return $this->identity;
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
     * @param $family
     * @param $identity
     * @param $amount
     * @param $description
     * @return Operation
     */
    public static function create($family, $identity, $amount, $description)
    {
        return new self($family, $identity, $amount, $description);
    }
}

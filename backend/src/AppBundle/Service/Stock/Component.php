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

use AppBundle\Entity\Component\ComponentInterface;

/**
 * Class Component
 * This class process stock transactions for components
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Component
{
    /**
     * @var Control
     */
    private $control;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * Component constructor.
     * @param Control $control
     */
    function __construct(Control $control)
    {
        $this->control = $control;
    }

    /**
     * @param ComponentInterface $component
     * @param $amount
     * @param $description
     * @return $this
     */
    public function add($family, $identity, $amount, $description)
    {
        $this->transactions[] = [
            'family' => $family,
            'identity' => $identity,
            'amount' => $amount,
            'description' => $description
        ];

        return $this;
    }

    /**
     * @param array $transactions
     */
    public function transact(array $transactions = [])
    {
        if (!empty($transactions)) {
            $this->transactions = $transactions;
        }

        $operations = [];
        foreach ($this->transactions as $transaction){

            $operation = Operation::create(
                $transaction['family'],
                $transaction['identity'],
                $transaction['amount'],
                $transaction['description']
            );

            $operations[] = $operation;
        }

        $this->control->process($operations);

        $this->transactions = [];
    }
}

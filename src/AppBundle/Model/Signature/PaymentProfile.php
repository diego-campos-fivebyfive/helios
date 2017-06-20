<?php

namespace AppBundle\Model\Signature;

class PaymentProfile
{
    use Common;

    /**
     * @var string
     */
    public $holder_name;

    /**
     * @var string
     */
    public $card_expiration;

    /**
     * @var string
     */
    public $card_number;

    /**
     * @var string
     */
    public $card_cvv;

    /**
     * @var int
     */
    public $customer_id;

    /**
     * @var int
     */
    public $terms;
}
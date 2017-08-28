<?php

namespace AppBundle\Model\Signature;

class Subscription implements CommonInterface
{
    use Common;

    const CODE_CREDIT_CARD = 'credit_card';
    const CODE_INVOICE = 'boleto_inovador';
    const CODE_CASH = 'cash';

    public $plan_id;

    public $customer_id;

    public $payment_method_code = 'credit_card';

    public $product_items;

    public $payment_profile;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->payment_profile = PaymentProfile::create();
    }
}
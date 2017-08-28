<?php

namespace AppBundle\Model\Signature;

/**
 * Class Signature
 * @package AppBundle\Model\Signature
 */
class Signature
{
    public $plan_id;

    public $customer_id;

    public $payment_method_code = 'credit_card';

    public $product_items;

    public $payment_profile;

    /**
     * Signature constructor.
     */
    function __construct()
    {
        $this->product_items = [];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return (get_object_vars($this));
    }

    /**
     * @param $subscription
     * @return Signature
     */
    public static function fromSubscription($subscription)
    {
        if(self::isSubscription($subscription)) {

            $signature = new self();

            $signature->plan_id = $subscription->plan->id;
            $signature->customer_id = $subscription->customer->id;

            foreach($subscription->product_items as $item){
                $signature->product_items[] = [
                    'product_id' => $item->id,
                    'quantity' => $item->quantity
                ];
            }

            return $signature;
        }

        throw new \InvalidArgumentException('Invalid subscription format');
    }

    /**
     * @param $plan
     * @return Signature
     */
    public static function fromPlan($plan)
    {
        $items = $plan->plan_items;

        if(count($items) < 2){
            throw new \InvalidArgumentException('Invalid configuration plan');
        }

        $signature = new self();

        $first = $items[0];
        $second = $items[1];

        $signature->product_items = [
            [ 'product_id' => $first->product->id,  'quantity' => 1 ],
            [ 'product_id' => $second->product->id,  'quantity' => 0 ]
        ];

        $signature->plan_id = $plan->id;

        return $signature;
    }

    /**
     * @param $subscription
     * @return bool
     */
    private static function isSubscription($subscription)
    {
        $vars = get_object_vars($subscription);

        if(!empty($vars) && array_key_exists('status', $vars)){
            return true;
        }

        return false;
    }
}
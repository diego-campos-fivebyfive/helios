<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\OrderInterface;

abstract class OrderFormatter
{
    public static function format(OrderInterface $order)
    {
        $data = [];

        if(null != $account = $order->getAccount()){
            $data['account_id'] = $account->getId();
        }

        $data = [
            'note' => (string) $order->getNote(),
            'description' => $order->getDescription(),
            'elements' => []
        ];

        $elements = [];
        foreach ($order->getElements() as $element){

            $elements[] = [
                'code' => $element->getCode(),
                'description' => $element->getDescription(),
                'quantity' => $element->getQuantity(),
                'unitPrice' => $element->getUnitPrice()
            ];
        }

        unset($data['elements']);

        $data['products'] = $elements;

        return $data;
    }
}
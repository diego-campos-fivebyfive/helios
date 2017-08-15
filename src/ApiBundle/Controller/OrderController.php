<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApiBundle\Controller;

use AppBundle\Entity\Order\Element;
use AppBundle\Service\Order\OrderFormatter;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Order\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractApiController
{
    /**
     * @param Order $order
     * @return Response
     */
    public function getOrderAction(Order $order)
    {
        return $this->responseApi($order);
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function putOrderAction(Order $order, Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $filerCodes = function($product){
            return $product['code'];
        };

        $codes = array_map($filerCodes, $content['products']);
        $products = array_combine($codes, $content['products']);

        $elementManager = $this->manager('order_element');

        /** @var Element $element */
        foreach ($order->getElements() as $element){
            $code = $element->getCode();
            if(!array_key_exists($code, $products)){
                $order->removeElement($element);
                // TODO: Check if cascade operation is working here
                $elementManager->delete($element);
            }else{

                $element
                    ->setUnitPrice($products[$code]['unitPrice'])
                    ->setQuantity($products[$code]['quantity']);

                $elementManager->save($element);
            }
        }

        $order->setDescription($content['description']);

        $this->manager('order')->save($order);

        return $this->responseApi($order);
    }

    /**
     * @param Order $order
     * @return Response
     */
    private function responseApi(Order $order)
    {
        $data = OrderFormatter::format($order);

        $view = View::create($data);

        return $this->handleView($view);
    }
}
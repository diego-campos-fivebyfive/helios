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

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Service\Order\ComponentCollector;
use AppBundle\Service\Order\ComponentExtractor;
use AppBundle\Service\Order\ElementResolver;
use AppBundle\Service\Order\OrderManipulator;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Order\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends AbstractApiController
{
    public function getOrdersAction()
    {
    }

    /**
     * @param Order $order
     * @return Response
     */
    public function getOrderAction(Order $order)
    {
        $fetchProducts = function(Order $order){

            $products = [];
            /** @var Element $element */
            foreach ($order->getElements() as $element) {
                $products[] = [
                    'id' => $element->getId(),
                    'code' => $element->getCode(),
                    'description' => $element->getDescription(),
                    'quantity' => $element->getQuantity(),
                    'unit_price' => $element->getUnitPrice(),
                    'family' => $element->getFamily()
                ];
            }

            return $products;
        };

        $fetchItems = function(Order $order) use($fetchProducts){

            $items = [];
            foreach ($order->getChildrens() as $children) {

                $items[] = [
                    'id' => $children->getId(),
                    'description' => $children->getDescription(),
                    'note' => $children->getNote(),
                    'products' => $fetchProducts($children)
                ];
            }

            return $items;
        };

        $data = [
            'id' => $order->getId(),
            'account_id' => $order->getAccount()->getId(),
            'description' => $order->getDescription(),
            'note' => $order->getNote(),
            'status' => $order->getStatus(),
            'memorial' => $order->getMetadata('memorial'),
            'isquik_id' => $order->getIsquikId()
        ];

        if($order->isBudget()){
            $data['items'] = $fetchItems($order);
        }else{
            $data['products'] = $fetchProducts($order);
        }

        return $this->handleView(
            View::create($data)
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function postOrdersAction(Request $request)
    {
        $data = $this->getData($request);

        //$items = $data['items'];
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->create();

        if (array_key_exists('parent_id', $data) && array_key_exists('products', $data)) {

            $parent = $manager->find($data['parent_id']);

            if (!$parent instanceof Order) {
                return $this->handleView(
                    View::create('The parent order not found', Response::HTTP_NOT_FOUND)
                );
            }

            /** @var ComponentCollector $collector */
            $collector = $this->get('component_collector');
            foreach ($data['products'] as $product) {

                $component = $collector->fromCode($product['code']);
                if ($component) {
                    $extract = ComponentExtractor::extract($component);

                    $product['metadata'] = $extract['metadata'];
                }

                $element = new Element();
                ElementResolver::update($element, $product);
                $order
                    ->addElement($element)
                    ->addElement($element->setFamily($product['family']));
            }

            $order
                ->setNote($data['note'])
                ->setDescription($data['description'])
                ->setAccount($parent->getAccount())
                ->setParent($parent)
                ->setStatus($data['status']);

            OrderManipulator::checkPower($order);

            $manager->save($order);

            return $this->getOrderAction($order);
        }

        $account = $this->manager('account')->find($data['account_id']);

        if (!$account instanceof AccountInterface || !$account->isAccount()) {
            return $this->handleView(
                View::create('Account not found', Response::HTTP_NOT_FOUND)
            );
        }

        $order
            ->setAccount($account)
            ->setNote($data['note'])
            ->setDescription($data['description'])
            ->setIsquikId($data['isquik_id'])
            ->setStatus($data['status']);

        $manager->save($order);

        return $this->getOrderAction($order);
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function putOrderAction(Order $order, Request $request)
    {
        $data = $this->getData($request);
        $manager = $this->manager('order');

        if($order->isBudget()){

            $order
                ->setNote($data['note'])
                ->setDescription($data['description'])
                ->setIsquikId($data['isquik_id'])
                ->setStatus($data['status']);

        }else {

            $products = $data['products'];

            $filterElement = function (Order $order, $idOrCode) {
                return $order->getElements()->filter(function (Element $element) use ($idOrCode) {
                    return $idOrCode === $element->getId() || $idOrCode === $element->getCode();
                })->first();
            };

            /** @var ComponentCollector $collector */
            $collector = $this->get('component_collector');
            foreach ($products as $product) {

                $element = $filterElement($order, $product['code']);

                if ($element instanceof Element) {

                    $component = $collector->fromCode($product['code']);

                    if ($component) {
                        $metadata = ComponentExtractor::extract($component);

                        $product['metadata'] = $metadata['metadata'];
                    }

                    ElementResolver::update($element, $product);
                }
            }
        }

        $manager->save($order);

        return $this->getOrderAction($order);
    }

    /**
     * @param Order $order
     * @return Response
     */
    public function deleteOrderAction(Order $order)
    {
        $this->manager('order')->delete($order);

        return $this->handleView(
            View::create([], Response::HTTP_ACCEPTED)
        );
    }

    /**
     * @param Order $order
     * @param Element $element
     * @return Response
     */
    public function getOrderElementAction(Order $order, Element $element)
    {
        $data = [
            'id' => $element->getId(),
            'code' => $element->getCode(),
            'description' => $element->getDescription(),
            'quantity' => $element->getQuantity(),
            'unit_price' => $element->getUnitPrice(),
            'tag' => $element->getTag()
        ];

        return $this->handleView(
            View::create($data)
        );
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function postOrderElementAction(Order $order, Request $request)
    {
        $data = $this->getData($request);

        $data['order'] = $order;

        $element = new Element();
        ElementResolver::update($element, $data);

        $this->manager('order')->save($order);

        return $this->handleView(
            View::create([], Response::HTTP_CREATED)
        );
    }

    /**
     * @param Order $order
     * @param Element $element
     * @param Request $request
     */
    public function putOrderElementAction(Order $order, Element $element, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        ElementResolver::update($element, $data);

        $this->manager('order_element')->save($element);

        return $this->handleView(
            View::create([], Response::HTTP_ACCEPTED)
        );
    }

    /**
     * @param Order $order
     * @param Element $element
     * @return Response
     */
    public function deleteOrderElementAction(Order $order, Element $element)
    {
        if ($order->getElements()->contains($element)) {

            $order->removeElement($element);

            $this->manager('order_element')->delete($element);

            return $this->json([], Response::HTTP_ACCEPTED);
        }

        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getData(Request $request)
    {
        return json_decode($request->getContent(), true);
    }
}

<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Manager\OrderManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class OrderCloner
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * @var array
     */
    private $orderMasterProperties = [
        'shippingRules',
        'contact',
        'email',
        'phone',
        'firstname',
        'postcode',
        'address',
        'city',
        'state',
        'cnpj',
        'ie',
        'source',
        'level',
        'deliveryAt',
        'deadline',
        'deliveryAddress',
        'lastname',
        'deliveryPostcode',
        'deliveryState',
        'deliveryCity',
        'deliveryDistrict',
        'deliveryStreet',
        'deliveryNumber',
        'deliveryComplement',
        'discount',
        'deliveryDelay',
        'billingDirect',
        'billingFirstname',
        'billingLastname',
        'billingContact',
        'billingCnpj',
        'billingIe',
        'billingPhone',
        'billingEmail',
        'billingPostcode',
        'billingState',
        'billingCity',
        'billingDistrict',
        'billingStreet',
        'billingNumber',
        'billingComplement',
        'shipping',
        'expireDays'
    ];

    /**
     * @var array
     */
    private $orderProperties = [
        'description',
        'note',
        'metadata',
        'insurance',
        'source',
        'level',
        'message',
        'power',
        'total',
    ];

    /**
     * @var array
     */
    private $elementProperties = [
        'code',
        'description',
        'quantity',
        'family',
        'metadata',
        'cmv',
        'tax'
    ];

    /**
     * MemorialCloner constructor.
     */
    function __construct(OrderManager $manager)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->manager = $manager;
    }

    /**
     * @param OrderInterface $source
     * @param $number
     * @param bool $subMasterClone
     * @return mixed
     */
    public function cloneOrder(OrderInterface $source, $number, $subMasterClone = false)
    {
        $orders = [];

        for ($i=0; $i<$number; $i++) {

            /** @var OrderInterface $newOrder */
            $newOrder = new Order();//$this->manager->create();


            if ($source->isMaster()) {

                $newOrder->setStatus(OrderInterface::STATUS_BUILDING);

                if ($source->getAgent())
                    $newOrder->setAgent($source->getAgent());
                if ($source->getAccount())
                    $newOrder->setAccount($source->getAccount());

                /** @var OrderInterface $children */
                foreach ($source->getChildrens() as $children)
                    $newOrder->addChildren($this->cloneOrder($children, 1, true));

                foreach ($this->orderProperties as $property) {

                    $value = $this->accessor->getValue($source, $property);

                    $this->accessor->setValue($newOrder, $property, $value);
                }

                foreach ($this->orderMasterProperties as $property) {

                    $value = $this->accessor->getValue($source, $property);

                    $this->accessor->setValue($newOrder, $property, $value);
                }

            } else {

                $newOrder = $this->cloneElements($source, $newOrder);

                foreach ($this->orderProperties as $property) {

                    $value = $this->accessor->getValue($source, $property);

                    $this->accessor->setValue($newOrder, $property, $value);
                }

                if ($subMasterClone)
                    return $newOrder;
                else
                    $newOrder->setParent($source->getParent());
            }

            $this->manager->save($newOrder);

            $orders[$i] = $newOrder;
        }

        if (!$source->isMaster())
            $this->manager->save($source->getParent());

        return $orders;
    }

    /**
     * @param OrderInterface $source
     * @param OrderInterface $newOrder
     * @return OrderInterface
     */
    private function cloneElements(OrderInterface $source, OrderInterface $newOrder)
    {
        foreach ($source->getElements() as $element) {
            $newElement = new Element();

            foreach ($this->elementProperties as $property) {

                $value = $this->accessor->getValue($element, $property);

                $this->accessor->setValue($newElement, $property, $value);
            }
            $newElement->setMarkup($element->getMarkup() / 100);
            $newElement->setDiscount($element->getDiscount() / 100);
            $newElement->setUnitPrice($element->getUnitPrice());

            $newOrder->addElement($newElement);
        }

        return $newOrder;
    }
}

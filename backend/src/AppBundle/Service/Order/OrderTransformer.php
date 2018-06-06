<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectAdditive;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Entity\Kit\Kit;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\ElementInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderAdditive;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Pricing\MemorialInterface;
use AppBundle\Manager\KitManager;
use AppBundle\Manager\OrderManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Transform project data into a new order
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderTransformer
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * OrderTransformer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $this->container->get('order_manager');
    }

    /**
     * @param ProjectInterface $project
     * @param bool $persist
     * @return OrderInterface
     */
    public function transformFromProject(ProjectInterface $project, $persist = true)
    {
        /** @var OrderInterface $order */
        $order = $this->manager->create();

        $collection = ComponentExtractor::fromProject($project);

        foreach ($collection as $data) {
            $element = ElementResolver::create($data);
            $order->addElement($element);
        }

        $order->setDescription(sprintf('Sistema de %skWp', $project->getPower()));
        $order->setShippingRules($project->getShippingRules());

        if($project->isPromotional())
            $order->setLevel(MemorialInterface::LEVEL_PROMOTIONAL);

        if ($project->isFiname())
            $order->setLevel(MemorialInterface::LEVEL_FINAME);

        $this->additiveTransfer($order, $project);
        OrderManipulator::updateDescription($order);

        if ($persist)
            $this->manager->save($order);

        return $order;
    }

    /**
     * @param array $childrens
     * @return OrderInterface
     */
    public function transformFromChildrens(array $childrens)
    {
        $this->normalizeChildrens($childrens);

        /** @var OrderInterface $order */
        $order = $this->manager->create();
        foreach ($childrens as $children){
            if(!$children->getParent()) {
                $order->addChildren($children);
                if (!$order->getAccount() && $children->getAccount()) {
                    $order->setAccount($children->getAccount());
                }
            }
        }

        $metadata = $order->getChildrens()->first()->getMetadata();

        $order->setMetadata($metadata);

        $this->manager->save($order);

        return $order;
    }

    /**
     * @param CartPool $cartPool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function transformFromCartPool(CartPool $cartPool)
    {
        $power = 0;
        $total = 0;
        foreach ($cartPool->getItems() as $item) {
            $power += $item['power'] * $item['quantity'];
            $total += $item['value'] * $item['quantity'];
        }

        $account = $cartPool->getAccount();
        $checkout = $cartPool->getCheckout();
        $shipping = json_decode($checkout['shipping'], true);

        /** @var Order $order */
        $order = $this->manager->create();

        $order->setLevel($account->getLevel());
        $order->setSource(OrderInterface::SOURCE_KIT);
        $order->setStatus(OrderInterface::STATUS_INSERTED);
        $order->setSubStatus(OrderInterface::SUBSTATUS_INSERTED_ON_BILLING);
        $order->setAccount($account);
        $order->setTotal($total);
        $order->setBillingCity($checkout['city']);
        $order->setBillingCnpj($checkout['documentNumber']);
        $order->setBillingComplement($checkout['complement']);
        $order->setBillingDistrict($checkout['neighborhood']);
        $order->setBillingEmail($checkout['email']);
        $order->setBillingPhone($checkout['phone']);
        $order->setBillingPostcode($checkout['zipcode']);
        $order->setBillingStreet($checkout['street']);
        $order->setBillingFirstname($checkout['firstName']);
        $order->setBillingLastname($checkout['lastName']);
        $order->setBillingNumber($checkout['number']);
        $order->setBillingState($checkout['state']);
        $order->setPower($power);
        $order->setAgent($account->getAgent());

        $deliveryKeys = [
            'street' => 'street',
            'city' => 'city',
            'complement' => 'complement',
            'number' => 'number',
            'postcode' => $checkout['differentDelivery'] ? 'postal_code' : 'zipcode',
            'district' => $checkout['differentDelivery'] ? 'district' : 'neighborhood',
            'state' => 'state'
        ];

        $values = $checkout['differentDelivery'] ? $shipping['address'] : $checkout;

        $this->setOrderDelivery($order, $values, $deliveryKeys);

        $items = $cartPool->getItems();

        /** @var KitManager $kitManager */
        $kitManager = $this->container->get('kit_manager');

        foreach ($items as $item) {
            $this->createSuborder($order, $account->getLevel(), $item);
            $this->debitKitStock($kitManager, $item);
        }

        $this->manager->save($order);
        $kitManager->flush();
    }

    /**
     * @param KitManager $kitManager
     * @param $item
     */
    private function debitKitStock(KitManager $kitManager, $item)
    {
        /** @var Kit $kit */
        $kit = $kitManager->find($item['sku']);

        if ($kit) {
            $stock = $kit->getStock() - $item['quantity'];
            $kit->setStock($stock);
        }

        $kitManager->save($kit, false);
    }

    /**
     * @param Order $order
     * @param $values
     * @param $keys
     */
    private function setOrderDelivery(Order $order, $values, $keys)
    {
        $order->setDeliveryStreet($values[$keys['street']]);
        $order->setDeliveryCity($values[$keys['city']]);
        $order->setDeliveryComplement($values[$keys['complement']]);
        $order->setDeliveryNumber($values[$keys['number']]);
        $order->setDeliveryPostcode($values[$keys['postcode']]);
        $order->setDeliveryDistrict($values[$keys['district']]);
        $order->setDeliveryState($values[$keys['state']]);
    }

    /**
     * @param Order $order
     * @param $level
     * @param array $item
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    private function createSuborder(Order $order, $level, array $item)
    {
        for ($i = 0; $i < $item['quantity']; $i++) {
            /** @var Order $children */
            $children = $this->manager->create();
            $children->setParent($order);
            $children->setDescription('Sistema de ' . $item['power'] . "kWp");
            $children->setPower($item['power']);
            $children->setSource(OrderInterface::SOURCE_KIT);
            $children->setLevel($level);
            $children->setStatus(OrderInterface::STATUS_INSERTED);
            $children->setSubStatus(OrderInterface::SUBSTATUS_INSERTED_ON_BILLING);
            $children->setTotal($item['value']);
            $childrenComponents = $this->loadComponents($item['components']);

            foreach ($childrenComponents as $component) {
                $this->addOrderElement(
                    $children,
                    $component['component']->getCode(),
                    $component['component']->getDescription(),
                    0,
                    $component['quantity'],
                    $component['family']);

            }

            $this->manager->save($children, false);
        }
    }

    /**
     * @param array $componentsArray
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    private function loadComponents(array $componentsArray)
    {
        $components = [];

        foreach ($componentsArray as $component) {
            $family = $component['family'];

            $manager = $this->container->get("{$family}_manager");

            /** @var QueryBuilder $qb */
            $qb = $manager->createQueryBuilder();
            $alias = $qb->getRootAlias();

            $qb->andWhere(
                $qb->expr()->eq("{$alias}.id", $component['componentId'])
            );

            $result = [
                'family' => $family,
                'quantity' => $component['quantity'],
                'component' => $qb->getQuery()->getSingleResult()
            ];

            $components[] = $result;
        }

        return $components;
    }

    /**
     * @param OrderInterface $order
     * @param $code
     * @param $description
     * @param $unitPrice
     * @param $quantity
     */
    private function addOrderElement(OrderInterface $order, $code, $description, $unitPrice, $quantity, $family, array $metadata = [])
    {
        $element = new Element();
        $element
            ->setCode($code)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setUnitPrice($unitPrice)
            ->setFamily($family)
            ->setMetadata($metadata)
        ;

        $order->addElement($element);
    }

    /**
     * @param array $childrens
     */
    private function normalizeChildrens(array &$childrens)
    {
        foreach ($childrens as $key => $children){
            if(is_numeric($children)){
                $childrens[$key] = $this->manager->find($children);
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @param ProjectInterface $project
     */
    private function additiveTransfer(OrderInterface $order, ProjectInterface $project)
    {
        /** @var ProjectAdditive $projectAdditive */
        foreach ($project->getProjectAdditives() as $projectAdditive) {
            /** @var OrderAdditive $orderAdditive */
            $orderAdditive = new OrderAdditive();

            $orderAdditive->setOrder($order);
            $orderAdditive->setAdditive($projectAdditive->getAdditive());
            $orderAdditive->setQuantity($projectAdditive->getQuantity());
            $orderAdditive->setType($projectAdditive->getType());
            $orderAdditive->setName($projectAdditive->getName());
            $orderAdditive->setDescription($projectAdditive->getDescription());
            $orderAdditive->setTarget($projectAdditive->getTarget());
            $orderAdditive->setValue($projectAdditive->getValue());
        }
    }
}

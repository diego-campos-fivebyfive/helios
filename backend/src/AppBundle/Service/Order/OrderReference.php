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

use AppBundle\Manager\OrderManager;
use AppBundle\Entity\Order\OrderInterface;

/**
 * Class OrderReference
 * This class generate orders reference with defined format
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderReference
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var array
     */
    private $lockDrivers = ['pdo_mysql'];

    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * @var string
     */
    private $sqlCount = /** @lang text */"SELECT count(id) as counter FROM %s WHERE created_at >= '%s' AND reference IS NOT NULL";

    /**
     * OrderReference constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param OrderInterface $order
     * @return null|string
     */
    public function generate(OrderInterface $order)
    {
        $this->checkOrder($order);

        $reference = $order->getReference();

        if(!$reference) {

            $date = new \DateTime();
            $table = $this->getTable($order);

            $sql = sprintf($this->sqlCount, $table, $date->format('Y-m-d'));

            $this->lockOrUnlockTable($table);

            try {

                $statement = $this->manager->getConnection()->query($sql);
                $row = $statement->fetch();

                $reference = sprintf('%02d%02d%02d%06d',
                    $date->format('y'),
                    $date->format('n'),
                    $date->format('j'),
                    $row['counter'] + 1
                );

                $this->manager->getConnection()->update($table, ['reference' => $reference], ['id' => $order->getId()]);

                $order->setReference($reference);

            } catch (\Exception $e) {
                // Exception is not handled!
            }

            $this->lockOrUnlockTable($table);
        }

        return $reference;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function getTable(OrderInterface $order)
    {
        return $this->manager->getTableName();
    }

    /**
     * @param $table
     */
    private function lockOrUnlockTable($table)
    {
        if(in_array($this->manager->getConnection()->getDriver()->getName(), $this->lockDrivers)){
            $sql = $this->isLocked ? sprintf('UNLOCK TABLES') : sprintf('LOCK TABLES %s WRITE', $table);
            $this->manager->getConnection()->exec($sql);
            $this->isLocked = !$this->isLocked;
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function checkOrder(OrderInterface $order)
    {
        if(!$order->getId())
            $this->exception('The order is not persisted');

        if($order->isChildren())
            $this->exception('Suborders can not receive reference');
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}

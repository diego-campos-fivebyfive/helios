<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Order;

use AppBundle\Entity\Misc\AdditiveInterface;

interface OrderAdditiveInterface
{

    /**
     * @param OrderInterface $order
     * @return OrderAdditiveInterface
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @return float
     */
    public function getAdditiveQuota();
}
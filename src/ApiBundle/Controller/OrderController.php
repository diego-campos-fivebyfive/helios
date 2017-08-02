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

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\Order;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

class OrderController extends FOSRestController
{
    public function getOrderAction(Order $id)
    {
        $order = $id;

        $data = [
            'id' => $order->getId(),
            'status' => $order->getStatus(),
            'account' => $order->getAccount(),
        ];

        $data['projects'] = $order->getProjects()->map(function (ProjectInterface $project) {
            return $project->getId();
        });

        $view = View::create($data);

        return $this->handleView($view);
    }

}
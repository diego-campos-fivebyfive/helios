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

use FOS\RestBundle\View\View;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends FOSRestController
{
    public function postOrdersAction(Request $request)
    {
        //$data = json_decode($request->getContent(), true);

        ///** @var AccountInterface $accountManager */
        //$account = $this->get('account_manager')->find($data['account']);

        ///** @var ProjectInterface $projects */
        //$projects = $this->get('project_manager')->find($data['projects']);

        //$orderManager = $this->get('order_manager');
        ///** @var OrderInterface $orderManager */
        //$order = $orderManager->create();
        //$order
            //->setStatus(1)
            //->setAccount($account);

        //foreach ($projects as $id) {
            ///** @var Project $project */
            //$project = $this->manager('project')->find($id);
            //$order->addProject($project);
        //}
        //$orderManager->save($order);

    }

    public function getOrderAction(Order $id)
    {
        $order = $id;

        $data = [
            'id' => $order->getId(),
            'status' => $order->getStatus(),
            'account' => $order->getAccount(),
            'projects' => $order->getProjects()->map(function (ProjectInterface $project) {
                return Array(
                    'Id' => $project->getId(),
                    'Metadata' => $project->getMetadata(),
                    'Address' => $project->getAddress(),
                    'Stage' => $project->getStage(),
                    'Infconsumption' => $project->getInfConsumption(),
                    'Power' => $project->getInfPower(),
                    'Efficiency' => $project->getEfficiencyLoss(),
                    'Latitude' => $project->getLatitude(),
                    'Longitude' => $project->getLongitude(),
                    'Roof' => $project->getRoofType(),
                    //'Extras' => $project->getProjectExtras(),
                    //'Structures' => $project->getProjectStructures(),
                    //'Inverters' => $project->getProjectInverters(),
                    //'Modules' => $project->getProjectModules(),
                    //'Stringboxes' => $project->getProjectStringBoxes(),
                    //'Varietys' => $project->getProjectVarieties(),
                    'StructureType' => $project->getStructureType(),
                    'Distribution' => $project->getDistribution(),
                    'Lifetime' => $project->getLifetime(),
                    'Create_at' => $project->getCreatedAt(),
                    'Update_at' => $project->getUpdatedAt(),
                    'Customer' => $project->getCustomer(),
                    'Token' => $project->getToken()
                );
            })
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }

}
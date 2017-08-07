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
                    'id' => $project->getId(),
                    'address' => $project->getAddress(),
                    'stage' => $project->getStage(),
                    'inf_consumption' => $project->getInfConsumption(),
                    'power' => $project->getInfPower(),
                    'efficiency' => $project->getEfficiencyLoss(),
                    'latitude' => $project->getLatitude(),
                    'longitude' => $project->getLongitude(),
                    'roof' => $project->getRoofType(),
                    'extras' => $project->getProjectExtras(),
                    'inverters' => $project->getProjectInverters(),
                    'modules' => $project->getProjectModules(),
                    'structures' => $project->getProjectStructures(),
                    'stringboxes' => $project->getProjectStringBoxes(),
                    'varietys' => $project->getProjectVarieties(),
                    'structure_type' => $project->getStructureType(),
                    'project_extra_products' => $project->getProjectExtraProducts(),
                    'project_extra_services' => $project->getProjectExtraServices(),
                    'project_taxes' => $project->getProjectTaxes(),
                    'sale_price' => $project->getSalePrice(),
                    'sale_price_equipments' => $project->getSalePriceEquipments(),
                    'sale_price_inverters' => $project->getSalePriceInverters(),
                    'sale_price_modules' => $project->getSalePriceModules(),
                    'sale_price_services' => $project->getSalePriceServices(),
                    'distribution' => $project->getDistribution(),
                    'lifetime' => $project->getLifetime(),
                    'cost_price_components' => $project->getCostPriceComponents(),
                    'cost_price' => $project->getCostPrice(),
                    'cost_price_total' => $project->getCostPriceTotal(),
                    'cost_price_extra' => $project->getCostPriceExtra(),
                    'annual_cost_operation' => $project->getAnnualCostOperation(),
                    'annual_production' => $project->getAnnualProduction(),
                    'accumulated_cash' => $project->getAccumulatedCash(),
                    'create_at' => $project->getCreatedAt(),
                    'cpdate_at' => $project->getUpdatedAt(),
                    'customer' => $project->getCustomer(),
                    'token' => $project->getToken()
                );
            })
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }

}
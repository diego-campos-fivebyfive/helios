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

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\ProjectInverterInterface;
use AppBundle\Entity\Component\ProjectModuleInterface;
use AppBundle\Entity\Component\ProjectStringBoxInterface;
use AppBundle\Entity\Component\ProjectStructureInterface;
use AppBundle\Entity\Component\ProjectVarietyInterface;
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

    public function getDataArray($order){
        return $data = [
            'id' => $order->getId(),
            'status' => $order->getStatus(),
            'account' => [
                'id' => $order->getAccount()->getId(),
                'firstname' => $order->getAccount()->getFirstName(),
                'lastname' => $order->getAccount()->getLastName(),
                'extraDocument' => $order->getAccount()->getExtraDocument(),
                'document' => $order->getAccount()->getDocument(),
                'email' => $order->getAccount()->getEmail(),
                'state' => $order->getAccount()->getState(),
                'city' => $order->getAccount()->getCity(),
                'phone' => $order->getAccount()->getPhone(),
                'district' => $order->getAccount()->getDistrict(),
                'street' => $order->getAccount()->getStreet(),
                'number' => $order->getAccount()->getNumber(),
                'postcode' => $order->getAccount()->getPostcode(),
                'status' => $order->getAccount()->getStatus(),
                'level' => $order->getAccount()->getLevel()
            ],
            'projects' => $order->getProjects()
                ->map(function (ProjectInterface $project) {

                    $inverters = $project->getProjectInverters()
                        ->map(function (ProjectInverterInterface $projectInverter) {
                            return [
                                'id' => $projectInverter->getId(),
                                'quantity' => $projectInverter->getQuantity(),
                                'price' => $projectInverter->getUnitCostPrice(),
                                'code' => $projectInverter->getInverter()->getCode(),
                                'description' => $projectInverter->getInverter()->getModel(),
                                'family' => 'inverter'
                            ];
                        })->toArray();
                    $modules = $project->getProjectModules()
                        ->map(function (ProjectModuleInterface $projectModule) {
                            return [
                                'id' => $projectModule->getId(),
                                'quantity' => $projectModule->getQuantity(),
                                'price' => $projectModule->getUnitCostPrice(),
                                'code' => $projectModule->getModule()->getCode(),
                                'description' => $projectModule->getModule()->getModel(),
                                'family' => 'module'
                            ];
                        })->toArray();
                    $structures = $project->getProjectStructures()
                        ->map(function (ProjectStructureInterface $projectStructure) {
                            return [
                                'id' => $projectStructure->getId(),
                                'quantity' => $projectStructure->getQuantity(),
                                'price' => $projectStructure->getUnitCostPrice(),
                                'code' => $projectStructure->getStructure()->getCode(),
                                'description' => $projectStructure->getStructure()->getDescription(),
                                'family' => 'structure'
                            ];
                        })->toArray();
                    $stringboxes = $project->getProjectStringBoxes()
                        ->map(function (ProjectStringBoxInterface $projectStringBox) {
                            return [
                                'id' => $projectStringBox->getId(),
                                'quantity' => $projectStringBox->getQuantity(),
                                'price' => $projectStringBox->getUnitCostPrice(),
                                'code' => $projectStringBox->getStringBox()->getCode(),
                                'description' => $projectStringBox->getStringBox()->getDescription(),
                                'family' => 'stringbox'
                            ];
                        })->toArray();
                    $varietys = $project->getProjectVarieties()
                        ->map(function (ProjectVarietyInterface $projectVariety) {
                            return [
                                'id' => $projectVariety->getId(),
                                'quantity' => $projectVariety->getQuantity(),
                                'price' => $projectVariety->getUnitCostPrice(),
                                'code' => $projectVariety->getVariety()->getCode(),
                                'description' => $projectVariety->getVariety()->getDescription(),
                                'family' => 'variety'
                            ];
                        })->toArray();

                    $products = array_merge($inverters, $modules, $structures, $stringboxes, $varietys);

                    return Array(
                        'id' => $project->getId(),
                        'products' => $products
                    );
                })->toArray()
        ];
    }

    public function getOrderAction(Order $order)
    {
        $data = $this->getDataArray($order);

        $view = View::create($data);
        return $this->handleView($view);
    }

    public function putOrderAction(Request $request, Order $order){
        $data = json_decode($request->getContent(), true);

        /** @var Order $orderManager */
        $orderManager = $this->get('order_manager');
        $order
            ->setStatus($data['status']);

        $projectManager = $this->get('project_manager');

        foreach ($data['projects'] as $proj) {
            /** @var Project $project */
            $project = $this->get('project_manager')->find($proj['id']);

            $products = $proj['products'];

            foreach ($products as $product){
                $family = $product['family'];
                $id = $product['id'];

                $projectProduct = null;
                switch ($family) {
                    case 'inverter':
                        $projectProduct = $project->getProjectInverters()->filter(function(ProjectInverterInterface $projectInverter) use($id){
                            return $projectInverter->getId() == $id;
                        })->first();
                        break;
                    case 'module':
                        $projectProduct = $project->getProjectModules()->filter(function(ProjectModuleInterface $projectModule) use($id){
                            return $projectModule->getId() == $id;
                        })->first();
                        break;
                    case 'structure':
                        $projectProduct = $project->getProjectStructures()->filter(function(ProjectStructureInterface $projectStructure) use($id){
                            return $projectStructure->getId() == $id;
                        })->first();
                        break;
                    case 'stringbox':
                        $projectProduct = $project->getProjectStringBoxes()->filter(function(ProjectStringBoxInterface $projectStringbox) use($id){
                            return $projectStringbox->getId() == $id;
                        })->first();
                        break;
                    case 'variety':
                        $projectProduct = $project->getProjectVarieties()->filter(function(ProjectVarietyInterface $projectVariety) use($id){
                            return $projectVariety->getId() == $id;
                        })->first();
                        break;
                }
                $projectProduct
                    ->setQuantity($product['quantity'])
                    ->setUnitCostPrice($product['price']);
            }
            $projectManager->save($project);
        }
        try {
            $orderManager->save($order);
            $status = Response::HTTP_CREATED;
            $data = $this->getDataArray($order);
        } catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'can not order update';
        }

        $view = View::create($data)->setStatusCode($status);
        return $this->handleView($view);
    }


}
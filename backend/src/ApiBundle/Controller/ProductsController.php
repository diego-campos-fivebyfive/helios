<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Costumer;
use AppBundle\Entity\Project\Project;
use AppBundle\Manager\InverterManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;

class ProductsController extends AbstractController
{
    public function postProductsAction(Request $request)
    {

        $inverterManager = $this->manager('inverter');
        $structureManager = $this->manager('structure');
        $moduleManager = $this->manager('module');

        $data = json_decode($request->getContent(), true);
        $products = $data['products'];

        foreach ($products as $product)
        {
            switch ($product['family']){
                case 'inverter':
                    /** @var Inverter $inverter */
                    $inverter = $inverterManager->create();
                    $inverter   ->setCode($product['code'])
                    ->setModel($product['description'])
                    ->setMaxEfficiency(0.97);
                    $inverterManager->save($inverter);
                    break;

                case 'structure':
                    /** @var Structure $structure */
                    $structure = $structureManager->create();
                    $structure  ->setCode($product['code'])
                        ->setDescription($product['description']);
                    $structureManager->save($structure);
                    break;

                case 'module':
                    /** @var Module $module */
                    $module = $moduleManager->create();
                    $module ->setCode($product['code'])
                        ->setModel($product['description'])
                        ->setCellNumber(35)
                        ->setEfficiency(60)
                        ->setTemperatureOperation(45)
                        ->setTempCoefficientMaxPower(-0.44)
                        ->setTempCoefficientVoc(-0.34)
                        ->setTempCoefficientIsc(0.055);
                    $moduleManager->save($module);
                    break;
                }
            }
    }
}

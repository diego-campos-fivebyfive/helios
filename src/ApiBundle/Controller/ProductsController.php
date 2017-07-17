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

    public function postMemorialAction(Request $request)
    {
        $rangeManager = $this->manager('range');
        $memorialManager = $this->manager('memorial');

        $data = json_decode($request->getContent(), true);
        $products = $data['products'];

        foreach ($products as $product)
        {
            $startAt = new \DateTime($data['start_at']);
            $endAt = new \DateTime($data['end_at']);

            $memorial = $memorialManager->create();
            $memorial->setName('Teste')
                ->setVersion($data['version'])
                ->setStartAt($startAt)
                ->setEndAt($endAt)
                ->setStatus(1);
            $memorialManager->save($memorial);

                    $markups = $product['markups'];

            foreach ($markups as $level => $config) {

                foreach ($config as $item) {
                    $range = $rangeManager->create();
                    $range->setCode($product['code'])
                        ->setMemorial($memorial)
                        ->setLevel($level)
                        ->setInitialPower($item['start'])
                        ->setFinalPower($item['end'])
                        ->setMarkup($item['markup'])
                        ->setPrice(35);
                    $rangeManager->save($range);
                }
            }
        }
    }
}

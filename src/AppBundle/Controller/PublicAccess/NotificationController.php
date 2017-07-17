<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/isquik", name="isquik_notifications")
     * @Method("POST")
     */
    public function postIndexAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $notifications = $data;

        switch ($notifications['callback']) {
            case 'product_create':
            case 'memorial_create':
            case 'account_create':
            case 'order_create':
        }
    }

    /**
     * @Route("/sices", name="sices_notifications")
     * @Method("GET")
     */
    public function getIndexAction(Request $request)
    {

    }

    public function ProductsCreate(Request $request)
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

    public function postMemorialCreate(Request $request)
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
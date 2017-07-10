<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\PricingManager;
use AppBundle\Entity\Component\ProjectItem;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Financial\ProjectFinancial;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Financial\Tax;
use AppBundle\Entity\ParameterManager;
use AppBundle\Entity\Project\NasaCatalog;
use AppBundle\Entity\Project\Project;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\Project\ProjectInverterInterface;
use AppBundle\Form\Financial\TaxType;
use AppBundle\Form\Project\NasaCatalogType;
use AppBundle\Form\Settings\KitPricingType;
use AppBundle\Model\KitPricing;
use AppBundle\Service\Component\PriceCalculator;
use AppBundle\Service\Component\ProjectPrecifier;
use AppBundle\Service\ProjectFilter;
use AppBundle\Service\ProjectHelper;
use AppBundle\Service\ProjectProcessor;
use AppBundle\Service\ProposalFilter;
use AppBundle\Service\Support\Project\Financial;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use AppBundle\Service\Woopra\Event;
use AppBundle\Util\ProjectPricing\CostPrice;
use AppBundle\Util\ProjectPricing\ProjectPricing;
use AppBundle\Util\ProjectPricing\SalePrice;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vindi\Exceptions\RequestException;

/**
 * @Route("debug")
 */
class DebugController extends AbstractController
{
    /**
     * @Route("/", name="debug_index")
     */
    public function indexAction(Request $request)
    {
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');

        /** @var \ApiBundle\Entity\Client $client */
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://localhost:8000/the_page'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code', 'client_credentials'));
        $clientManager->updateClient($client);

        dump($client); die;
    }

    /**
     * @Route("/pricing")
     */
    public function pdfPageAction()
    {
        /** @var \AppBundle\Entity\Component\ProjectInterface $project */
        $project = $this->manager('project')->find(103);

        dump($project); die;
        $costPrice = new CostPrice();

        $costPrice->calculate($project); die;

        $costProducts = $project->getCostPriceExtraProducts();
        $costServices = $project->getCostPriceExtraServices();
        $costComponents = $project->getCostPriceComponents();

        /** @var PricingManager $pricingManager */
        $pricingManager = $this->get('app.kit_pricing_manager');
        $margins = $pricingManager->findAll();

        $percentEquipments = 0;
        $percentServices   = 0;

        /** @var KitPricing $margin */
        foreach ($margins as $margin){

            //dump( $margin->target . " - " . $margin->percent );

            switch($margin->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $percentEquipments += $margin->percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $percentServices += $margin->percent;
                    break;
                case KitPricing::TARGET_GENERAL:
                    $percentServices += $margin->percent;
                    $percentEquipments += $margin->percent;
                    break;
            }
        }


        $saleCalculator = new SalePrice();
        $saleCalculator->calculate($project, $percentEquipments, $percentServices);

        $this->manager('project')->save($project);

        dump($project->getSalePrice()); die;

        $projectPricing->setCostEquipments($costProducts + $costComponents);
        $projectPricing->setCostServices($costServices);

        dump($projectPricing); die;

        $projectPricing->calculate();

        $tax = .1;
        $markup = 1;

        die;

        foreach($project->getProjectModules() as $projectModule){
            //$projectModule->getModule()->setCurrentPrice(1000);

            $price = $projectModule->getModule()->getCurrentPrice();

            $projectModule->setUnitCostPrice(
                PriceCalculator::calculatePrice($price, $markup, $tax)
            );
            //dump($projectModule);
        }

        foreach ($project->getProjectInverters() as $projectInverter){
            $projectInverter->getInverter()->setCurrentPrice(1000);
        }

        dump($project);
        die;
        $this->manager('project')->save($project);

        die;
        //dump($project->getProjectItemsServices()); die;

        $projectModules = $project->getProjectModules();
        $projectInverters = $project->getProjectInverters();
        $projectExtraProducts = $project->getProjectItemsProducts();
        $projectExtraServices = $project->getProjectItemsServices();

        dump($project->getProjectModules()->toArray()); die;


        /** @var \AppBundle\Entity\Component\ProjectModuleInterface $projectModule */
        $projectModule = $projectModules->first();
        /** @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter */
        $projectInverter = $projectInverters->first();

        $pricingParameters = $this->container->get('app.kit_pricing_manager')->findAll();

        $percentGeneral = 0;
        $percentEquipments = 0;
        $percentServices = 0;

        foreach ($pricingParameters as $pricingParameter){

            $percent = (float) $pricingParameter->percent;

            switch($pricingParameter->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $percentEquipments += $percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $percentServices += $percent;
                    break;
                case KitPricing::TARGET_GENERAL:
                    $percentGeneral += $percent;
                    break;
            }
        }

        $costEquipments = $project->getCostPriceComponents();
        $costServices = $project->getCostPriceExtraServices();

        $costTotal = $project->getCostPriceTotal();

        dump($costEquipments);
        dump($costServices);
        dump($costTotal);
        dump($percentEquipments);
        dump($percentServices);
        dump($percentGeneral);

        /*
        $finalCost = $this->getFinalCost();
        $this->priceSaleEquipments = (100 * $finalCost) / (100-($this->getTotalPercentEquipments(true)));
        $this->priceSaleServices = (100 * $this->getTotalPriceServices()) / (100-($this->getTotalPercentServices(true)));
        $this->priceSale = $this->getPriceSaleEquipments() + $this->getPriceSaleServices();
        */

        $salePriceEquipments = (100 * $costTotal) / (100 - ($percentEquipments));
        //$salePriceServices = (100 * $)

        dump($salePriceEquipments); die;

        //dump($pricingParameters); die;
        //$projectInverter->setUnitCostPrice(6000);
        //$this->manager('project_inverter')->save($projectInverter);
        //$projectModule->setUnitCostPrice(200);
        //$this->manager('project_module')->save($projectModule);
        //dump($projectModule->getUnitCostPrice());

        dump($project->getCostPriceModules()); die;
    }


    private function calculatePrice()
    {

    }
}

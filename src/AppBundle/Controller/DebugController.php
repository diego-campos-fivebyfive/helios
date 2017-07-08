<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\PricingManager;
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
use AppBundle\Service\ProjectFilter;
use AppBundle\Service\ProjectHelper;
use AppBundle\Service\ProjectProcessor;
use AppBundle\Service\ProposalFilter;
use AppBundle\Service\Support\Project\Financial;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use AppBundle\Service\Woopra\Event;
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

        $projectModules = $project->getProjectModules();
        $projectInverters = $project->getProjectInverters();

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

        $costTotal = $project->getCostPriceTotal();

        dump($costEquipments);
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
}

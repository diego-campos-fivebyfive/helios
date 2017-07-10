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
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Level;
use AppBundle\Entity\Pricing\Range;

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
     * @Route("/teste", name="debug_teste")
     */
    public function testeAction()
    {
        $level = $this->manager('level');
        $range = $this->manager('range');

        $manager = $this->manager('memorial');

        $memorial = $manager->create();
        $memorial->setName('módulo ABX14');
        $memorial->setVersion(4.2);
        $memorial->setStartAt(new \DateTime());
        $memorial->setEndAt(new \DateTime());
        $memorial->setStatus(1);

        $manager->save($memorial);

        dump($memorial); die;

    }

    /**
     * @Route("/pdf-page")
     */
    public function pdfPageAction()
    {

    }
}

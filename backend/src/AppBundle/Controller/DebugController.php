<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\PricingManager;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Model\KitPricing;
use AppBundle\Service\Mailer;
use Aws\S3\S3Client;
use Doctrine\Common\Inflector\Inflector;
use Exporter\Exporter;
use Exporter\Handler;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Exporter\Writer\CsvWriter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("debug")
 * //@Security("has_role('ROLE_SUPER_ADMIN')")
 */
class DebugController extends AbstractController
{
    /**
     * @Route("/cmv-import")
     */
    public function importCmvAction()
    {
        $kernel = $this->get('kernel');
        $file = $kernel->getCacheDir() . '/memorial.csv';

        $content = file_get_contents($file);
        $data = explode("\n", $content);
        array_shift($data);

        /** @var \AppBundle\Service\Order\ComponentCollector $collector */
        $collector = $this->get('component_collector');

        //dump($collector->getManager('inverter')->getConnection()->getHost()); die;

        $managers = $collector->getManagers();

        //dump($managers); die;

        foreach ($data as $info){
            if(!empty($info)){

                list($code, $name, $cmvProtheus, $cmvApplied) = explode(';', $info);

                $component = $collector->fromCode($code);

                if($component instanceof ComponentInterface){

                    $class = array_reverse(explode('\\', get_class($component)));

                    $type = Inflector::tableize($class[0]);

                    $component
                        ->setCmvProtheus($cmvProtheus)
                        ->setCmvApplied($cmvApplied)
                    ;

                    $managers[$type]->save($component);
                }
            }
        }

        foreach ($managers as $manager){
            $manager->flush();
        }

        die;
    }

    /**
     * @Route("/components-import")
     */
    public function importComponentsAction()
    {
        die;
        $dir = $this->get('kernel')->getRootDir() . '/../web/public/';
        $products = ['inverter', 'module'];

        foreach ($products as $product) {

            $manager = $this->manager($product);

            $data = explode("\n", file_get_contents($dir . $product . '.csv'));

            unset($data[0]);

            foreach($data as $key => $info){

                if(strlen($info)) {

                    list($code, $description) = explode(';', $info);

                    $entity = $manager->findOneBy(['code' => $code]);

                    if ($entity) {
                        $entity->setDescription($description);
                        //dump($entity->getCode() . ' >> ' . $entity->getDescription());
                    } else {
                        //dump($info);
                    }

                    if($entity)
                        $manager->save($entity, $key == count($data));
                }
            }
        }
        die;
    }

    /**
     * @Route("/components-export")
     */
    public function exportComponentsAction()
    {
        die;
        $products = ['inverter', 'module', 'string_box', 'structure', 'variety'];

        foreach ($products as $product) {

            $filename = $this->get('kernel')->getRootDir() . sprintf('/../web/public/%s.csv', $product);

            $writer = new CsvWriter($filename);

            $query = $this->manager($product)->createQueryBuilder()->getQuery();

            $iterator = new DoctrineORMQuerySourceIterator($query, ['code', 'description']);

            Handler::create($iterator, $writer)->export();
        }
        die;
    }

    /**
     * @Route("/logger/{env}", name="debug_logger")
     */
    public function loggerAction($env, Request $request)
    {
        $file = $this->get('kernel')->getRootDir() . '/logs/' . $env . '.log';

        $content = '';
        if(is_readable($file)){
            $content = file_get_contents($file);
        }

        echo $content; die;
    }

    /**
     * @Route("/head/logger/{date}")
     */
    public function headCheckerAction($date)
    {
        $kernel = $this->get('kernel');
        $env = $kernel->getEnvironment();

        $file = $kernel->getRootDir() . sprintf('/cache/%s/fetch_head_%s.json', $env, $date);

        if(file_exists($file)) {
            $data = explode("\n", file_get_contents($file));

            if (is_array($data)) {
                foreach ($data as $content) {
                    if(strlen($content)) {
                        $extract = json_decode(substr($content, 0, -1), true);

                        foreach ($extract as $info => $server) {
                            echo $info . "<br>";
                            foreach ($server as $key => $extra) {
                                echo $key . " => ";
                                if (is_string($extra)) {
                                    echo $extra;
                                }
                                if (is_array($extra)) {
                                    echo json_encode($extra);
                                }
                                echo "<br>";
                            }

                            echo "<hr>";
                        }
                    }
                }
            }
        }

        die;
    }

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

    /**
     * @Route("/range", name="debug_range")
     */
    public function testRangeAction(){

        $manager = $this->manager('memorial');

        /** @var Memorial $memorial */
        $memorial = $manager->create();
        $memorial
            ->setStatus(1)
            ->setStartAt(new \DateTime)
            ->setEndAt(new \DateTime('1 month'))
            ->setName('Teste')
            ->setVersion('0001');

        for($i = 1; $i <= 10; $i++) {

            $range = new Range();
            $range
                ->setMemorial($memorial)
                ->setCode('6473' * $i)
                ->setInitialPower(150 * $i)
                ->setFinalPower(300 * $i)
                ->setLevel('gold')
                ->setMarkup(.1)
                ->setPrice(2500 * $i);

            $memorial->addRange($range);
        }

        $manager->save($memorial);
        dump($memorial);
        die;

//        $manager = $this->manager('range');
//
//        $qb = $manager->getEntityManager()->createQueryBuilder();
//
//        $qb->select('r')->from(Range::class, 'r');
//        $qb->where('r.code = :code');
//        $qb->andwhere('r.level = :level');
//        $qb->andWhere('r.initialPower <= :power');
//        $qb->andWhere('r.finalPower >= :power');
//
//        $qb->setParameters([
//            'code' => 6473,
//            'level' => 'gold',
//            'power' => 200
//        ]);
//
//        $query = $qb->getQuery();
//
//        $result = $query->getOneOrNullResult();

//        $project = new Project();
//
//        $projectPricifier = new ProjectPrecifier($this->manager('project'));
//
//        $projectPricifier->priceCost($project);
//
//        dump($projectPricifier); die();

    }

    /**
     * @Route("/order", name="debug_order")
     */
    public function orderAction()
    {
        /** @var Customer $accounts */
        $accounts = $this->manager('customer')->find(19);
        /** @var Project $project */
        $project = $this->manager('project')->find(102);
        $manager = $this->manager('order');

        /** @var OrderInterface $order */
        $order = $manager->create();
        $order->setStatus(1)
              ->setAccount($accounts);
        $project->setOrder($order);

        $manager->save($order);
        //
    }

   /**
    * @Route("/s3/buckets", name="debug_s3_buckets")
    */
   public function s3Action()
   {
        $s3 = $this->get('aws.s3');
        $data = $s3->listBuckets();
        $buckets = $data['Buckets'];

       dump($buckets); die;
   }

    /**
    * @Route("/s3/objects", name="debug_s3_objects")
    */
    public function s3ObjectsAction()
    {
        $s3 = $this->get('aws.s3');
        $iterator = $s3->getIterator('ListObjects', array(
            'Bucket' => 'pss-homolog-private'
        ));

        foreach ($iterator as $object) {
            echo $object['Key'] . "\n";
        }

        //dump($iterator); die;
    }

    /**
    * @Route("/s3/download", name="debug_s3_download")
    */
    public function s3DownloadAction()
    {
        $path = "{$this->container->get('kernel')->getRootDir()}/../..";
        $file = 'order/proforma/proforma_429_20171017-101633_.pdf';

        $s3 = $this->get('aws.s3');
        $file = $s3->getObject([
            'Bucket' => 'pss-homolog-private',
            'Key' => $file,
            'SaveAs' => "{$path}/.uploads/{$file}"
        ]);

        dump($file); die;
    }
}

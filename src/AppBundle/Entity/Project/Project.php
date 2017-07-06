<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\KitComponentInterface;
use AppBundle\Entity\Component\KitElementInterface;
use AppBundle\Entity\Component\KitInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\TokenizerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Project
 *
 * @ORM\Table(name="app_project_legacy")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Project implements ProjectInterface
{
    use TokenizerTrait;
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=25, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="default_chart_data", type="text", nullable=true)
     */
    private $defaultChartData;

    /**
     * @var string
     *
     * @ORM\Column(name="chart_data", type="text", nullable=true)
     */
    private $chartData;

    /**
     * @var json
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var json
     *
     * @ORM\Column(name="snapshot", type="json", nullable=true)
     */
    private $snapshot;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Financial\ProjectFinancial
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Financial\ProjectFinancial", mappedBy="project")
     */
    private $financial;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project\ProjectInverter", mappedBy="project", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $inverters;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project\BudgetSection", mappedBy="project")
     */
    private $sections;

    /**
     * @var \AppBundle\Entity\Component\Kit
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Component\Kit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kit_id", referencedColumnName="id")
     * })
     */
    private $kit;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \AppBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sale_stage", referencedColumnName="id")
     * })
     */
    private $saleStage;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     * })
     */
    private $member;

    function __construct()
    {
        $this->inverters = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->metadata = [];
        $this->snapshot = [];
    }

    function __clone()
    {
        $this->id = null;
        $this->token = null;
        $this->snapshot = null;
        $this->defaultChartData = null;
        $this->chartData = null;

        unset($this->metadata['email']);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getNumber()
    {
        if(null != $number = $this->getMetadata('number')){
            return sprintf('%04d', $number);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @inheritDoc
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @inheritDoc
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @inheritDoc
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @inheritDoc
     */
    public function setKit(KitInterface $kit)
    {
        $this->kit = $kit;

        $this->checkDefinitions();

        //$this->generateProjectInverters();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getKit()
    {
        return $this->kit;
    }

    /**
     * @inheritDoc
     */
    public function setCustomer(BusinessInterface $customer)
    {
        if (!$customer->isCustomer())
            $this->unsupportedDefinitionException(self::ERROR_UNSUPPORTED_CUSTOMER);

        $this->customer = $customer;

        $this->checkDefinitions();

        if(!$this->id && !$this->getAddress()){

            $this->setAddress($customer->getAddress())
                ->setLatitude($customer->getLatitude())
                ->setLongitude($customer->getLongitude());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @inheritDoc
     */
    public function setSaleStage(CategoryInterface $saleStage)
    {
        $this->saleStage = $saleStage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSaleStage()
    {
        return $this->saleStage;
    }

    /**
     * @inheritDoc
     */
    public function setMember(BusinessInterface $member)
    {
        if (!$member->isMember())
            $this->unsupportedDefinitionException(self::ERROR_UNSUPPORTED_MEMBER);

        $this->member = $member;

        $this->checkDefinitions();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @inheritDoc
     */
    public function addInverter(ProjectInverterInterface $inverter)
    {
        if (!$this->inverters->contains($inverter)) {
            $this->inverters->add($inverter);
        }
        return $this;
    }

    public function isAnalysable()
    {
        return empty($this->getAnalysisErrors());
    }

    /**
     * @inheritDoc
     */
    public function getAnalysisErrors()
    {
        $errors = [];

        if (!$this->hasMetadata()) {
            $errors[] = 'empty_calculation_metadata';
        }

        if (!$this->kit) {
            $errors[] = 'undefined_kit';
        } else {
            if ($this->kit->getPriceSale() <= 0) {
                $errors[] = 'negative_selling_price';
            }

            /**
             * TODO Checked for acceptance of processes with number of modules in the project different from the number of modules in the kit
             * Only price strategy is KitInterface::PRICE_STRATEGY_ABS
             */
            if(KitInterface::PRICE_STRATEGY_ABS == $this->kit->getInvoicePriceStrategy()) {
                $modules = $this->getConfiguredModules();
                foreach ($modules as $module) {
                    if ($module['applied'] != $module['available']) {
                        $errors[] = 'incompatible_number_of_modules';
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function removeInverter(ProjectInverterInterface $inverter)
    {
        if ($this->inverters->contains($inverter)) {
            $this->inverters->removeElement($inverter);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInverters()
    {
        return $this->inverters;
    }

    /**
     * @inheritDoc
     */
    public function getKitInverters()
    {
        return $this->inverters->map(function (ProjectInverterInterface $inverter) {
            return $inverter->getInverter();
        });
    }

    /**
     * @inheritDoc
     */
    public function addSection(BudgetSectionInterface $section)
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeSection(BudgetSectionInterface $section)
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        $power = 0;
        foreach ($this->inverters as $projectInverter) {
            $power += $projectInverter->getPower();
        }

        return $power;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        if($this->financial){
            if($this->financial->getProposal()){
                return $this->financial->getFinalPrice();
            }
        }

        return 0;
    }

    /**
     * @deprecated
     * @inheritDoc
     */
    public function setCalculationChart($calculationChart)
    {
        return $this->setDefaultChartData($calculationChart);
    }

    /**
     * @deprecated
     * @inheritDoc
     */
    public function getCalculationChart()
    {
        return $this->getDefaultChartData();
    }

    /**
     * @inheritDoc
     */
    public function setDefaultChartData($defaultChartData)
    {
        $this->defaultChartData = $defaultChartData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultChartData()
    {
        return $this->defaultChartData;
    }

    /**
     * @inheritDoc
     */
    public function setChartData($chartData)
    {
        $this->chartData = $chartData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChartData()
    {
        return $this->chartData;
    }

    /**
     * @inheritDoc
     */
    public function isComputable()
    {
        return empty($this->getErrors());
    }

    /**
     * @inheritDoc
     */
    public function getConfiguredModules()
    {
        $modules = [];

        if ($this->kit) {

            foreach ($this->kit->getModules() as $kitModule) {

                $data = $kitModule->getModule()->toArray();

                $data['available']  = $kitModule->getQuantity();
                $data['applied']    = 0;
                $data['price_sale'] = $kitModule->getUnitPriceSale();

                $modules[$kitModule->getModule()->getId()] = $data;
            }

            foreach ($this->inverters as $projectInverter) {
                foreach ($projectInverter->getModules() as $projectModule) {
                    if ($projectModule instanceof ProjectModuleInterface) {
                        $projectKitModule = $projectModule->getModule();
                        if ($projectKitModule instanceof KitComponentInterface) {

                            $module = $projectKitModule->getModule();

                            $data = $modules[$module->getId()];

                            $data['applied'] += $projectModule->countModules();
                            $modules[$module->getId()] = $data;
                        }
                    }
                }
            }
        }

        return $modules;
    }

    /**
     * @inheritDoc
     */
    public function getErrors($deep = true)
    {
        $errors = [];

        if (!$this->kit) {
            $errors[] = new ProjectError('project.error.undefined_kit');
        }

        if($this->inverters->count()) {
            foreach ($this->inverters as $projectInverter) {
                $errors[] = $projectInverter->getErrors();
            }
        }else{
            $errors[] = new ProjectError('project.error.undisclosed');
        }

        if ($deep)
            $errors = $this->deepErrors($errors);

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function getTotalArea()
    {
        $area = 0;
        foreach ($this->inverters as $projectInverter) {
            if ($projectInverter instanceof ProjectInverterInterface) {
                foreach ($projectInverter->getModules() as $projectModule) {
                    $area += $projectModule->getTotalArea();
                }
            }
        }

        return $area;
    }

    /**
     * @inheritDoc
     */
    public function getModules()
    {
        //$ids = [];
        $modules =[]; // new ArrayCollection();
        foreach ($this->getInverters() as $projectInverter){
            foreach ($projectInverter->getModules() as $projectModule){
                if($projectModule instanceof ProjectModuleInterface){
                    $id = $projectModule->getModule()->getModule()->getId();
                    $quantity = $projectModule->countModules();

                    if(array_key_exists($id, $modules)){
                        $module = $modules[$id];
                        $quantity += $module->quantity;
                    }

                    $projectModule->quantity = $quantity;

                    $modules[$id] = $projectModule;
                }
            }
        }

        return $modules;
    }

    /**
     * @inheritDoc
     */
    public function getTotalModules()
    {
        $totalModules = 0;
        $configuredModules = $this->getConfiguredModules();

        foreach ($configuredModules as $configuredModule) {
            $totalModules += $configuredModule['applied'];
        }

        return $totalModules;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataOperations()
    {
        $metadata = [];
        foreach ($this->inverters as $projectInverter) {
            $metadata[] = $projectInverter->getMetadataOperation();
        }

        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata($key, $metadata)
    {
        $this->metadata[$key] = $metadata;

        /**
         * TODO: This code will be moved to a method in the future
         */
        if('number' == $key){
            $this->number = (int) $metadata;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null, $default = null)
    {
        if ($key) {
            return $this->hasMetadata($key) ? $this->metadata[$key] : $default;
        }

        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function hasMetadata($key = null)
    {
        if ($this->metadata) {
            if (!$key) {
                return !empty($this->metadata);
            }
            return array_key_exists($key, $this->metadata);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAnnualProduction()
    {
        if (null != $kwhYear = $this->getMetadata('kwh_year'))
            return $kwhYear;

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getMonthlyProduction($deep = false)
    {
        $production = [];
        if ($this->hasMetadata('areas')) {
            foreach ($this->getMetadata('areas') as $area) {
                $months = $area['months'];
                foreach ($months as $month => $data) {
                    if (!array_key_exists($month, $production))
                        $production[$month] = 0;

                    $production[$month] += $data['total_month'];
                }
            }
        }

        return $deep ? $production : array_values($production);
    }

    /**
     * @param array $errors
     * @return array
     */
    private function deepErrors(array $errors)
    {
        $deepErrors = [];
        foreach ($errors as $error) {
            if ($error instanceof ProjectErrorInterface)
                $deepErrors[] = $error;

            if (is_array($error)) {
                $deepErrors = array_merge($deepErrors, $this->deepErrors($error));
            }
        }

        return $deepErrors;
    }

    /**
     * @inheritDoc
     */
    public function toArray($legacy = true)
    {
        if ($legacy) {

            $data = [
                'id' => $this->id,
                'token' => $this->token,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
                'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
                'kit' => $this->kit->toArray(),
                'areas' => []
            ];

            foreach ($this->getInverters() as $projectInverter) {
                $data['areas'][] = $projectInverter->toArray();
            }

            return $data;
        }

        return [
            'id' => $this->id,
            'token' => $this->token,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'total_area' => $this->getTotalArea(),
            'annual_production' => $this->getAnnualProduction(),
            'total_power' => $this->getPower(),
            'chart_data' => $this->getChartData()
        ];
    }

    /**
     * @inheritDoc
     */
    public function parseFilename()
    {
        if ($this->customer instanceof BusinessInterface) {
            $number = $this->getNumber();
            $names = explode(' ', $this->customer->getFirstname());

            return sprintf('%s.%s.pdf', $number, $names[0]);
        }

        return md5($this->token) . '.pdf';
    }

    /**
     * @inheritDoc
     */
    public function hasProposal()
    {
        if ($this->financial) {
            return $this->financial->hasProposal();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDone()
    {
        return null != $filename = $this->getMetadata('filename');
    }

    /**
     * @inheritDoc
     */
    public function setFinancial(ProjectFinancialInterface $financial = null)
    {
        $this->financial = $financial;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFinancial()
    {
        return $this->financial;
    }

    /**
     * @inheritDoc
     */
    public function getFinalCost()
    {
        return $this->getCostOfEquipments() + $this->getFreightPrice();
    }

    /**
     * @inheritDoc
     */
    public function getCostOfEquipments()
    {
        return $this->getTotalPriceComponents() + $this->getTotalPriceElements();
    }

    /**
     * @inheritDoc
     */
    public function getCostTotal()
    {
        return $this->getFinalCost() + $this->getTotalPriceServices();
    }

    /**
     * @inheritDoc
     */
    public function getFreightPrice()
    {
        return $this->getCostOfEquipments() * ($this->kit->getDeliveryBasePrice() / 100);
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceServices()
    {
        $price = 0;
        foreach ($this->kit->getElementServices() as $kitElement){
            if($kitElement->isIncremental()){
                $price += $kitElement->getUnitPrice() * $this->getTotalModules();
                continue;
            }

            $price += $kitElement->getPrice();
        }
        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceComponents()
    {
        $price = 0;
        foreach($this->getInverters() as $projectInverter) {
            if($projectInverter instanceof ProjectInverterInterface){

                $kitInverter = $projectInverter->getInverter();

                if($kitInverter instanceof KitComponentInterface) {

                    $price += 1 * $kitInverter->getPrice();

                    foreach($projectInverter->getModules() as $projectModule){
                        if($projectModule instanceof ProjectModuleInterface){

                            $kitModule = $projectModule->getModule();
                            if($kitModule instanceof KitComponentInterface) {
                                $price += $projectModule->countModules() * $kitModule->getPrice();
                            }
                        }
                    }
                }
            }
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPriceElements()
    {
        $price = 0;
        foreach ($this->kit->getElementItems() as $kitElement){
            if($kitElement->isIncremental()){
                $price += $kitElement->getUnitPrice() * $this->getTotalModules();
                continue;
            }

            $price += $kitElement->getPrice();
        }
        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSaleEquipments()
    {
        $priceModules = 0 ;
        $priceInverters = 0;
        foreach($this->getInverters() as $projectInverter){

            $priceInverters += $projectInverter->getInverter()->getUnitPriceSale();

            foreach($projectInverter->getModules() as $projectModule) {
                if($projectModule instanceof ProjectModuleInterface) {
                    /** @var \AppBundle\Entity\Component\KitComponentInterface $kitModule */
                    $kitModule = $projectModule->getModule();
                    $priceModules += ($projectModule->countModules() * $kitModule->getUnitPriceSale());
                }
            }
        }

        $priceItems = 0;
        foreach($this->getElementItems() as $elementItem){
            $priceItems += $elementItem['totalPriceSale'];
        }

        return ($priceModules + $priceInverters + $priceItems);
    }

    /**
     * @inheritDoc
     */
    public function getPriceSaleServices()
    {
        $price = 0;
        $totalModules = $this->getTotalModules();
        foreach($this->kit->getElementServices() as $service){
            if($service instanceof KitElementInterface) {
                if($service->isIncremental()){
                    $price += ($totalModules * $service->getUnitPriceSale());
                }else {
                    $price += $service->getTotalPriceSale();
                }
            }
        }

        return $price;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSale()
    {
        return $this->getPriceSaleEquipments() + $this->getPriceSaleServices();
    }

    /**
     * @inheritDoc
     */
    public function getElementItems()
    {
        $elements = [];
        $countModules = $this->getTotalModules();

        foreach($this->kit->getElementItems()->toArray() as $item){
            if($item instanceof KitElementInterface){

                $quantity = $item->isIncremental() ? $countModules : $item->getQuantity();
                $unitPriceSale = $item->getUnitPriceSale();

                $elements[] = [
                    'name' => $item->getName(),
                    'rate' => $item->getRate(),
                    'quantity' => $quantity,
                    'unitPriceSale' => $unitPriceSale,
                    'totalPriceSale' => $unitPriceSale * $quantity,
                    'isIncremental' => $item->isIncremental()
                ];
            }
        }

        return $elements;
    }

    /**
     * @inheritDoc
     */
    public function getElementServices()
    {
        $elements = [];
        $countModules = $this->getTotalModules();
        $cost = $this->getTotalPriceServices();
        $sale = $this->getPriceSaleServices();

        foreach($this->kit->getElementServices()->toArray() as $item){
            if($item instanceof KitElementInterface){

                $percent = $item->getUnitPrice() / $cost;
                $quantity = $item->isIncremental() ? $countModules : $item->getQuantity();
                $unitPriceSale = $percent * $sale;

                $elements[] = [
                    'name' => $item->getName(),
                    'rate' => $item->getRate(),
                    'quantity' => $quantity,
                    'unitPriceSale' => $unitPriceSale,
                    'totalPriceSale' => $unitPriceSale * $quantity
                ];
            }
        }

        return $elements;
    }

    /**
     * @inheritDoc
     */
    public function assertKitDistribution(KitInterface $previousKit = null)
    {
        if ($this->kit instanceof KitInterface) {

            /**
             * If there is a previous kit, check if it is the same as defined in the project
             */
            if ($previousKit && $previousKit->getId() != $this->kit->getId()) {
                return false;
            }

            /**
             * Checks whether the total count of inverters is equal to that distributed in the project
             */
            if ($this->kit->countInverters() != $this->inverters->count()) {
                return false;
            }

            // TODO: Risk Checkers!
            if (1 == 2) {
                /**
                 * Checks whether the total count of modules is equal to that distributed in the project
                 */
                if ($this->kit->countModules() != $this->getTotalModules()) {
                    return false;
                }

                /**
                 * Verifies that the inverters in the kit have the same id of the inverters distributed in the project
                 */
                $distInverterIds = $this->inverters->map(function (ProjectInverterInterface $projectInverter) {
                    return $projectInverter->getInverter()->getInverter()->getId();
                })->toArray();

                $kitInverterIds = $previousKit->getInverters()->map(function (KitComponentInterface $kitComponent) {
                    return $kitComponent->getInverter()->getId();
                })->toArray();

                if (!empty(array_diff($kitInverterIds, $distInverterIds))) {
                    return false;
                }

                /**
                 * Verifies that the modules in the kit have the same id of the modules distributed in the project
                 */
                $distModuleIds = [];
                $this->inverters->forAll(function ($key, ProjectInverterInterface $projectInverter) use (&$distModuleIds) {
                    $distModules = $projectInverter->getModules()->map(function (ProjectModuleInterface $projectModule) {
                        return $projectModule->getModule()->getModule()->getId();
                    })->toArray();
                    $distModuleIds = array_merge($distModuleIds, $distModules);
                });

                $kitModuleIds = $previousKit->getModules()->map(function (KitComponentInterface $kitComponent) {
                    return $kitComponent->getModule()->getId();
                })->toArray();

                //dump($distModuleIds);
                //dump($kitModuleIds);
                //die;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->generateToken();
    }

    public function clearRelations()
    {
        if($this->kit){
            $this->snapshot['kit'] = $this->kit->snapshot();
            $this->kit = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @param $message
     */
    private function unsupportedDefinitionException($message)
    {
        throw new \InvalidArgumentException($message);
    }

    /**
     * Check account definitions from: Customer, Member and Kit
     */
    private function checkDefinitions()
    {
        $customerAccount = null;
        $memberAccount = null;
        $kitAccount = null;

        if ($this->customer instanceof BusinessInterface)
            $customerAccount = $this->customer->getMember()->getAccount();

        if ($this->member instanceof BusinessInterface)
            $memberAccount = $this->member->getAccount();

        if ($this->kit instanceof KitInterface)
            $kitAccount = $this->kit->getAccount();

        if ($customerAccount && $memberAccount && ($customerAccount->getId() != $memberAccount->getId())) {
            $this->unsupportedDefinitionException('Customer account and member account are not referenced');
        }

        if ($customerAccount && $kitAccount && ($customerAccount->getId() != $kitAccount->getId())) {
            $this->unsupportedDefinitionException('Customer account and kit account are not referenced');
        }

        if ($memberAccount && $kitAccount && ($memberAccount->getId() != $kitAccount->getId())) {
            $this->unsupportedDefinitionException('Member account and kit account are not referenced');
        }
    }

    /**
     * Auto generate project inverters
     * 1. Fetch KitComponent Inverters
     * 2. Generate instances of ProjectInverters
     */
    private function generateProjectInverters()
    {
        if ($this->kit) {

            $this->resetProjectInverters();

            foreach ($this->kit->getInverters() as $kitInverter) {

                if ($kitInverter instanceof KitComponentInterface) {

                    for ($i = 0; $i < $kitInverter->getQuantity(); $i++) {

                        $inverter = $kitInverter->getInverter();

                        if ($inverter instanceof InverterInterface) {

                            $projectInverter = new ProjectInverter();
                            $projectInverter
                                ->setProject($this)
                                ->setInverter($kitInverter);
                        }
                    }
                }
            }
        }
    }

    private function resetProjectInverters()
    {
        if (!$this->inverters->isEmpty() && $this->kit->getId() != $this->inverters->first()->getInverter()->getKit()->getId()) {
            foreach ($this->inverters as $inverter) {
                $this->removeInverter($inverter);
            }
        }
    }
}


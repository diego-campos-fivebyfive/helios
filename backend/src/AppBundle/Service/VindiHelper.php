<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Model\Signature\Customer;
use AppBundle\Model\Signature\PaymentProfile;
use AppBundle\Model\Signature\Subscription;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vindi\Exceptions\RequestException;

class VindiHelper
{
    const INVOICE_EXTRA = 'invoice_extra';

    private $keys = [
        'app' => 'SnI3ucoSVVWER5dl8ichxfnhByUyB5_h',
        'sandbox' => 'scd0dVzV91bgJEt00vHVLXXtY5lrIkJn'
        //'sandbox' => 'SnI3ucoSVVWER5dl8ichxfnhByUyB5_h'
    ];

    private $plans = [
        'app' => 29739,
        'sandbox' => 27797
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $environment = 'sandbox';

    /**
     * @var array
     */
    private $services = [
        'customer'          => \Vindi\Customer::class,
        'plan'              => \Vindi\Plan::class,
        'subscription'      => \Vindi\Subscription::class,
        'bill'              => \Vindi\Bill::class,
        'payment_profile'   => \Vindi\PaymentProfile::class,
        'product_item'      => \Vindi\ProductItem::class,
        'charge'            => \Vindi\Charge::class
    ];

    /**
     * VindiHelper constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->handleEnvironment();

        putenv(sprintf('%s=%s', \Vindi\Vindi::$apiKeyEnvVar, $this->getApiKey()));
    }

    /**
     * @param $id
     * @return \AppBundle\Model\Signature\Common|object
     */
    public function getCustomer($id)
    {
        $customer = Customer::create();

        try{

            $customer->setEntity($this->getCustomerService()->get($id));

        }catch (RequestException $exception){

            $customer->setErrors(['not_found' => $exception->getMessage()]);
        }

        return $customer;
    }

    /**
     * @param Customer $customer
     */
    public function createCustomer(Customer &$customer)
    {
        try {

            $customer->setEntity($this->getCustomerService()->create($customer->toArray()));

        }catch (RequestException $exception){
            $customer->setErrors($this->handleRequestException($exception));
        }
    }

    /**
     * @param Customer $customer
     */
    public function updateCustomer(Customer &$customer)
    {
        try{

            $customer->setEntity($this->getCustomerService()->update($customer->id, $customer->toArray()));

        }catch (RequestException $exception) {
            $customer->setErrors($this->handleRequestException($exception));
        }
    }

    /**
     * @param $id
     * @return \AppBundle\Model\Signature\PaymentProfile|object
     */
    public function getPaymentProfile($id)
    {
        /**
         * Preventing multiple uploading of payment profiles when the payment profile is null
         * Vindi patterns:
         * / get all payment profiles
         * /{id} get a specific payment profile
         * Error: If {id} is null, the pattern is changed to /
         */
        if(!$id){
            return null;
        }

        $paymentProfile = PaymentProfile::create();

        try{

            $paymentProfile->setEntity($this->getPaymentProfileService()->get($id));

        }catch (RequestException $exception){

            $paymentProfile->setErrors(['not_found' => $exception->getMessage()]);
        }

        return $paymentProfile;
    }

    /**
     * @param PaymentProfile $paymentProfile
     */
    public function createPaymentProfile(PaymentProfile &$paymentProfile)
    {
        try{

            $paymentProfile->setEntity($this->getPaymentProfileService()->create($paymentProfile->toArray()));

        }catch (RequestException $exception){
            $paymentProfile->setErrors($this->handleRequestException($exception));
        }
    }

    /**
     * @param $id
     * @return \AppBundle\Model\Signature\Common|object
     */
    public function getSubscription($id)
    {
        $subscription = Subscription::create();

        try{

            $subscription->setEntity($this->getSubscriptionService()->get($id));

        }catch (RequestException $exception){
            $subscription->setErrors(['not_found' => $exception->getMessage()]);
        }

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function createSubscription(Subscription &$subscription)
    {
        try{

            if($subscription->payment_profile && $subscription->payment_profile->id){
                $subscription->payment_profile->ensure('id');
            }

            $subscription->setEntity($this->getSubscriptionService()->create($subscription->toArray()));

        }catch (RequestException $exception){
            $subscription->setErrors($this->handleRequestException($exception));
        }
    }

    /**
     * @param $id
     */
    public function cancelSubscription($id)
    {
        $service = $this->getSubscriptionService();

        $subscription = $service->get($id);

        if('canceled' == $subscription->status){
            return;
        }

        $this->getSubscriptionService()->delete($subscription->id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPlan($id = null)
    {
        if(!$id) {
            $id = $this->plans[$this->environment];
        }

        return $this->getPlanService()->get($id);
    }

    /**
     * @param $customer
     * @return null
     */
    /*public function getSubscription($customer)
    {
        if(!is_object($customer)){
            throw new \InvalidArgumentException('Invalid customer object');
        }

        if($customer instanceof BusinessInterface){
            $customer = $this->getCustomer($customer);
        }

        $subscriptions = $this->getSubscriptions([
            'customer_id' => $customer->id,
            'status' => 'active'
        ]);

        if(empty($subscriptions)){
            return null;
        }

        return $subscriptions[0];
    }*/

    public function getSubscriptions(array $query = [])
    {
        $string = '';

        foreach ($query as $key => $value){
            $string .= sprintf('%s=%s', $key, $value);
        }

        return $this->getSubscriptionService()->all([
            'query' => $string
        ]);
    }

    /**
     * @param RequestException $exception
     * @return array
     */
    public function handleRequestErrors(RequestException $exception)
    {
        $errors = [];
        foreach($exception->getErrors() as $error){
            $errors[$error->parameter] = $this->normalizeErrorMessage($error);
        }

        return $errors;
    }

    /**
     * @return array|mixed|string
     */
    public function getPlans()
    {
        return $this->getPlanService()->all();
    }

    /**
     * @return \Vindi\Customer
     */
    public function getCustomerService()
    {
        return $this->getService('customer');
    }

    /**
     * @return \Vindi\Plan
     */
    public function getPlanService()
    {
        return $this->getService('plan');
    }

    /**
     * @return \Vindi\Subscription
     */
    public function getSubscriptionService()
    {
        return $this->getService('subscription');
    }

    /**
     * @return \Vindi\Bill
     */
    public function getBillService()
    {
        return $this->getService('bill');
    }

    /**
     * @return \Vindi\PaymentProfile
     */
    public function getPaymentProfileService()
    {
        return $this->getService('payment_profile');
    }

    /**
     * @return \Vindi\ProductItem
     */
    public function getProductItemService()
    {
        return $this->getService('product_item');
    }

    /**
     * @return \Vindi\Charge
     */
    public function getChargeService()
    {
        return $this->getService('charge');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->keys[$this->environment];
    }

    /**
     * @return int
     */
    public function getPlanId()
    {
        return $this->plans[$this->environment];
    }

    /**
     * @param $error
     * @return mixed
     */
    private function normalizeErrorMessage($error)
    {
        $sources = [
            'holder_name' => [
                'não pode ficar em branco' => 'O nome do titular do cartão não pode ficar em branco'
            ],
            'card_expiration' => [
                'não deve estar expirado' => 'O cartão informado está expirado',
                'não pode ficar em branco' => 'A data de expiração não pode ficar em branco',
                'não é uma data' => 'A data de expiração e inválida'
            ],
            'card_number' => [
                'inválido(a)' => 'O número do cartão é inválido'
            ],
            'card_cvv' => [
                'inválido(a)' => 'O código de verificação é inválido'
            ],
            'registry_code' => [
                'inválido(a)' => 'O documento informado é inválido'
            ],
            'address.country' => [
                'não é um código ISO 3166-1 alpha-2 válido' => 'O país informado está em formato inválido'
            ],
            'address.state' => [
                'não é um código ISO 3166-2 válido' => 'O estado informado está em formato inválido'
            ],
            'email' => [
                'inválido(a)' => 'O email informado não é válido'
            ],
            'customer_id' => [
                'não pode ficar em branco' => 'O id do cliente não foi informado'
            ]
        ];

        if(array_key_exists($error->parameter, $sources)){
            $source = $sources[$error->parameter];
            if(array_key_exists($error->message, $source)){
                return $source[$error->message];
            }
        }

        return $error->parameter . ': ' . $error->message;
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getService($id)
    {
        if(!array_key_exists($id, $this->services)){
            throw new \InvalidArgumentException(sprintf('The service %s is not registered', $id));
        }

        if(is_object($this->services[$id])){
            return $this->services[$id];
        }

        $this->services[$id] = new $this->services[$id];

        return $this->getService($id);
    }

    /**
     * @param RequestException $exception
     * @return array
     */
    private function handleRequestException(RequestException $exception)
    {
        $errors = [];
        foreach ($exception->getErrors() as $error){
            $errors[$error->parameter] = $this->normalizeErrorMessage($error);
        }

        return $errors;
    }

    /**
     * @return \AppBundle\Manager\CustomerManager|object
     */
    private function getCustomerManager()
    {
        return $this->container->get('customer_manager');
    }

    /**
     * dynamic handle environment
     */
    private function handleEnvironment()
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        $host = $requestStack->getCurrentRequest()->getHost();

        preg_match('/(app\\.inovadorsolar|sandbox\\.inovadorsolar)/', $host, $matches);

        if(!empty($matches)){
            $sep = explode('.', $matches[0]);
            $this->environment = $sep[0];
        }

        /*var_dump('<br>HOST: ' . $host);
        var_dump('<br> ENV: '. $this->environment);
        var_dump('<br> PLAN: ' . $this->getPlanId());
        var_dump('<br> API: ' . $this->getApiKey());
        die;*/
    }
}
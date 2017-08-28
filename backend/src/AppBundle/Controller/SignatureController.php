<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Form\Extra\SignatureCustomerType;
use AppBundle\Form\Extra\SignatureType;
use AppBundle\Form\Signature\CustomerType;
use AppBundle\Form\Signature\PaymentProfileType;
use AppBundle\Form\Signature\SubscriptionType;
use AppBundle\Model\Signature\Customer;
use AppBundle\Model\Signature\PaymentProfile;
use AppBundle\Model\Signature\ProductItem;
use AppBundle\Model\Signature\Signature;
use AppBundle\Model\Signature\Subscription;
use AppBundle\Service\VindiHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vindi\Exceptions\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("signature")
 * @Security("has_role('ROLE_OWNER')")
 * @Breadcrumb("Signature")
 */
class SignatureController extends AbstractController
{
    /**
     * @Route("/customer/create", name="signature_customer_create")
     */
    public function customerCreateAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $member = $this->getCurrentMember();

        // Check customer exists
        $signature = $member->getSignature();
        if(null != $id = $signature['customer']){
            $vindiCustomer = $vindi->getCustomer($id);
            // Customer exist
            if($vindiCustomer->isValid()){
                if('archived' != $vindiCustomer->getEntity()->status){
                    return $this->customerUpdateAction($request);
                }
            }
        }

        /** @var \AppBundle\Model\Signature\Customer $customer */
        $customer = Customer::create([
            'name' => $member->getName()
        ]);

        $form = $this->createForm(CustomerType::class, $customer, [
            'action' => $this->generateUrl('signature_customer_create')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $customer->code = $member->getToken();
            $customer->email = $member->getEmail();
            //$customer->addPhone($member->getPhone());

            $vindi->createCustomer($customer);

            if($customer->isValid()){

                $this->addSignatureAttribute('customer', $customer->id);

                return $this->jsonResponse([], Response::HTTP_CREATED);
            }

            return $this->jsonResponse([
                'error' => $customer->getError()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('signature.form_customer', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/update", name="signature_customer_update")
     */
    public function customerUpdateAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $member = $this->getCurrentMember();
        $signature = $member->getSignature();

        $customer = $vindi->getCustomer($signature['customer']);

        $form = $this->createForm(CustomerType::class, $customer, [
            'action' => $this->generateUrl('signature_customer_update')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $customer->code = $member->getToken();

            $vindi->updateCustomer($customer);

            if($customer->isValid()){
                return $this->jsonResponse([], Response::HTTP_ACCEPTED);
            }

            return $this->jsonResponse([
                'error' => $customer->getError(),
                'errors' => $customer->getErrors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('signature.form_customer', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/payment_method/update", name="signature_payment_method_update")
     */
    public function paymentMethodUpdateAction(Request $request)
    {
        $error = null;
        $vindi = $this->getVindiHelper();
        $subscription = $this->getSubscription();

        if($subscription && $subscription->isValid()){

            $methodCode = $request->get('method_code');

            try{

                $vindi->getSubscriptionService()->update($subscription->id, [
                    'payment_method_code' => $methodCode
                ]);

                return $this->jsonResponse();

            }catch (RequestException $exception){
                $errors = $vindi->handleRequestErrors($exception);
                if(!empty($errors)){
                    $error = array_values($errors)[0];
                }
            }
        }

        return $this->jsonResponse([
            'error' => $error
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * @Route("/payment_profile/create", name="signature_payment_profile_create")
     */
    public function paymentProfileCreateAction(Request $request)
    {
        /** @var \AppBundle\Model\Signature\PaymentProfile $paymentProfile */
        $paymentProfile = PaymentProfile::create();

        $form = $this->createForm(PaymentProfileType::class, $paymentProfile, [
            'action' => $this->generateUrl('signature_payment_profile_create')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // TODO: Validate here!
            $signature = $this->getCurrentMember()->getSignature();
            $paymentProfile->customer_id = $signature['customer'];

            $vindi = $this->getVindiHelper();
            $vindi->createPaymentProfile($paymentProfile);

            if($paymentProfile->isValid()){

                // Create subscription reference on payment profile
                try {
                    if (null != $subscription = $signature['subscription']) {
                        $vindi->getSubscriptionService()->update($subscription, [
                            'payment_method_code' => Subscription::CODE_CREDIT_CARD,
                            'payment_profile' => ['id' => $paymentProfile->id]
                        ]);
                    }
                }catch (RequestException $exception){
                    //
                }

                $this->addSignatureAttribute('payment_profile', $paymentProfile->id);

                return $this->jsonResponse([], Response::HTTP_CREATED);
            }

            return $this->jsonResponse([
                'error' => $paymentProfile->getError()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('signature.form_payment_profile', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/subscription/create", name="signature_subscription_create")
     */
    public function subscriptionCreateAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $account = $this->getCurrentAccount();

        $plan = $vindi->getPlanService()->get($vindi->getPlanId());
        $planItems = $plan->plan_items;

        $main = $planItems[0];
        $extra = $planItems[1];

        /** @var \AppBundle\Model\Signature\Subscription $subscription */
        $subscription = Subscription::create();

        $subscription->product_items = [
            ProductItem::create(['product_id' => $main->product->id, 'quantity' => 1]),
            ProductItem::create(['product_id' => $extra->product->id, 'quantity' => 0])
        ];

        $form = $this->createForm(SubscriptionType::class, $subscription, [
            'action' => $this->generateUrl('signature_subscription_create')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /*return $this->jsonResponse([
                'invoice_url' => 'https://api.aceitafacil.com/boleto/6e3105a1-ebd4-4021-ae6a-a7d9ea10a62c/'
            ], Response::HTTP_CREATED);*/

            $signature = $this->getCurrentMember()->getSignature();

            $subscription->plan_id = $vindi->getPlanId();
            $subscription->customer_id = $signature['customer'];
            $subscription->payment_profile->id = $signature['payment_profile'];

            // Remove Payment Profile
            if(Subscription::CODE_INVOICE == $subscription->payment_method_code){
                $subscription->payment_profile = null;
            }

            $vindi->createSubscription($subscription);

            if($subscription->isValid()) {

                $bills = $vindi->getBillService()->all([
                    'query' => sprintf('subscription_id=%d', $subscription->id)
                ]);

                if (empty($bills)) {
                    return $this->jsonResponse([
                        'error' => 'Falha ao obter dados da fatura'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $bill = $bills[0];
                $charge = $bill->charges[0];
                $transaction = $charge->last_transaction;

                if ('paid' == $bill->status || Subscription::CODE_INVOICE == $subscription->payment_method_code) {

                    if(null == $signature['subscription'] && Subscription::CODE_INVOICE == $subscription->payment_method_code){
                       
                        //$expireAt = new \DateTime($charge->due_at);
                        //$interval = date_interval_create_from_date_string('4 days');
                        //$expireAt->add($interval);
                        $account->setStatus(BusinessInterface::STATUS_LOCKED);
                        //$account->setExpireAt($expireAt);
                        $this->manager('customer')->save($account);
                    }

                    $this->addSignatureAttribute('subscription', $subscription->id);

                    return $this->jsonResponse([
                        'invoice_url' => $charge->print_url
                    ], Response::HTTP_CREATED);
                }

                if('credit_card' == $subscription->payment_method_code) {

                    $vindi->getSubscriptionService()->delete($subscription->getEntity()->id);

                    return $this->jsonResponse([
                        'error' => sprintf('%s - Código: %s', $transaction->gateway_message, $transaction->gateway_response_code)
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            return $this->jsonResponse([
                'error' => $subscription->getError()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('signature.form_subscription', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="signature")
     */
    public function indexAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $member = $this->getCurrentAccount()->getOwner();
        $signature = $member->getSignature();

        // Check current subscription
        if(null != $id = $signature['subscription']){

            $subscription = $vindi->getSubscription($id);

            if($subscription->getEntity()){

                if(Subscription::ACTIVE == $subscription->getEntity()->status){

                    $customer = $vindi->getCustomer($signature['customer']);

                    if(null != $paymentProfile = $vindi->getPaymentProfile($signature['payment_profile'])){
                        $paymentProfile = $paymentProfile->getEntity();
                    }

                    // HANDLE BETA USERS
                    // DO NOT USAGE CREDIT CARD
                    if(!$paymentProfile){

                        $card = PaymentProfile::create();

                        $paymentProfile = array_merge([
                            'payment_company' => [
                                'code' => null,
                                'name' => null
                            ],
                            'card_number_last_four' => null
                        ], $card->toArray());
                    }

                    return $this->render('signature.index', [
                        'customer' => $customer,
                        'subscription' => $subscription,
                        'payment_profile' => $paymentProfile,
                        'payment_method' => $subscription->getEntity()->payment_method
                    ]);
                }
            }
        }

        return $this->redirectToRoute('signature_create');
    }

    /**
     * @Route("/payments", name="signature_payments")
     */
    public function paymentsAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('signature');
        }

        $account = $this->getCurrentAccount();
        $owner = $account->getOwner();
        $signature = $owner->getSignature();

        $vindi = $this->getVindiHelper();

        $subscription = $vindi->getSubscription($signature['subscription']);

        if (!$subscription) {
            return $this->redirectToRoute('signature_create');
        }

        $subscription = $subscription->getEntity();
        $perPage = 6;
        $page = $request->query->getInt('page', 1);

        $bills = $vindi->getBillService()->all([
            'query' => sprintf('customer_id=%d', $subscription->customer->id),
            'sort_order' => 'desc',
            'per_page' => $perPage,
            'page' => $page
        ]);

        $response = $vindi->getBillService()->getLastResponse();
        $headers = $response->getHeaders();

        $total = (int)$headers['Total'][0];

        $paginator = $this->getPaginator();
        $pagination = $paginator->paginate($bills, $page, $perPage);

        $pagination->setItems($bills);
        $pagination->setTotalItemCount($total);

        return $this->render('signature.payments', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/create", name="signature_create")
     */
    public function createAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $member = $this->getCurrentAccount()->getOwner();
        $signature = $member->getSignature();

        // Check current subscription
        if(null != $id = $signature['subscription']) {
            $subscription = $vindi->getSubscription($id);
            if ($subscription->getEntity() && Subscription::ACTIVE == $subscription->getEntity()->status) {

                return $this->redirectToRoute('signature');
            }
        }

        $plan = $vindi->getPlan($vindi->getPlanId());

        return $this->render('signature.config', [
            'plan' => $plan
        ]);
    }

    /**
     * @Route("/update", name="signature_update")
     * @Method("post")
     */
    public function updateAction(Request $request)
    {
        $vindi = $this->getVindiHelper();
        $account = $this->getCurrentAccount();
        $customer = $vindi->getCustomer($account->getOwner());
        $subscription = $vindi->getSubscription($customer);

        if (!$subscription) {
            return $this->redirectToRoute('signature_create');
        }

        $signature = Signature::fromSubscription($subscription);

        $form = $this->createSignatureForm($signature);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $target = $request->get('target');

            switch ($target) {
                case 'card':

                    try {

                        $vindi->getSubscriptionService()->update($subscription->id, [
                            'payment_profile' => $signature->payment_profile
                        ]);

                    } catch (\Vindi\Exceptions\RequestException $e) {

                        $errors = array_values($vindi->handleRequestErrors($e));

                        return $this->jsonResponse([
                            'message' => $errors[0],
                            'data' => $e->getErrors()
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                    return $this->jsonResponse([
                        'message' => 'Perfil de pagamento atualizado com sucesso!'
                    ], Response::HTTP_OK);

                    break;
                case 'plan':

                    $item = $subscription->product_items[1];
                    $product = $signature->product_items[1];

                    if($product['quantity'] > $item->quantity){

                        $request->request->set('quantity', $product['quantity']);
                        return $this->upgradeAction($request);
                    }

                    try {

                        $vindi->getProductItemService()->update($item->id, [
                            'quantity' => $product['quantity']
                        ]);

                        $account->setMaxMember($product['quantity']+1);
                        $this->manager('customer')->save($account);

                    } catch (\Vindi\Exceptions\RequestException $e) {

                        $errors = array_values($vindi->handleRequestErrors($e));

                        return $this->jsonResponse([
                            'message' => $errors[0],
                            'data' => $e->getErrors()
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                    return $this->jsonResponse([
                        'message' => 'Configuração de plano atualizada com sucesso!'
                    ], Response::HTTP_OK);

                    break;

                case 'charge':

                    $id = $request->get('target_id');

                    try {

                        $bill = $vindi->getBillService()->get($id);
                        $charge = null;

                        foreach ($bill->charges as $billCharge) {
                            if ('pending' == $billCharge->status) {
                                $charge = $billCharge;
                                break;
                            }
                        }

                        if (!$charge) {
                            return $this->jsonResponse([
                                'message' => 'Cobrança não encontrada'
                            ], Response::HTTP_NOT_FOUND);
                        }

                        //if($charge){

                        try {

                            $subscription = $vindi->getSubscriptionService()->update($subscription->id, [
                                'payment_profile' => $signature->payment_profile
                            ]);

                            $sendCharge = $vindi->getChargeService()->charge($charge->id, [
                                'id' => $subscription->payment_profile->id
                            ]);

                            if ('paid' == $sendCharge->status) {

                                return $this->jsonResponse([
                                    'message' => 'Pagamento efetuado com sucesso!'
                                ], Response::HTTP_OK);
                            }

                            $transaction = $sendCharge->last_transaction;

                            return $this->jsonResponse([
                                'message' => sprintf('%s - Código: %s', $transaction->gateway_message, $transaction->gateway_response_code)
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);


                        } catch (RequestException $e) {

                            $errors = array_values($vindi->handleRequestErrors($e));

                            return $this->jsonResponse([
                                'message' => $errors[0],
                                'data' => $e->getErrors()
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        //}
                    } catch (RequestException $e) {

                        return $this->jsonResponse([
                            'message' => 'Falha ao solicitar informações',
                        ], Response::HTTP_NOT_FOUND);
                    }

                    break;
            }

            return $this->jsonResponse([
                'message' => 'Testando update',
                'data' => $signature->toArray()
            ], Response::HTTP_CONFLICT);
        }

        return $this->jsonResponse([
            'message' => 'Dados inválidos'
        ], Response::HTTP_CONFLICT);
    }

    /**
     * @Route("/upgrade", name="signature_upgrade")
     * //@Method("post")
     */
    public function upgradeAction(Request $request)
    {
        $vindi = $this->getVindiHelper();

        $account = $this->getCurrentAccount();
        $owner = $account->getOwner();

        $signature = $owner->getSignature();

        //$customer = $vindi->getCustomer($signature['customer']);
        $subscription = $vindi->getSubscription($signature['subscription'])->getEntity();

        $quantity = $request->request->get('quantity');

        $updatedAt = new \DateTime();
        $periodEndAt = new \DateTime($subscription->current_period->end_at);
        $days = $periodEndAt->diff($updatedAt)->days;

        $items = $subscription->product_items;

        $main = $items[0];
        $extra = $items[1];

        // Downgrade
        if($quantity < $extra->quantity){

            $vindi->getProductItemService()->update($extra->id, [
                'quantity' => $quantity
            ]);

            $account->setMaxMember($quantity+1);
            $this->manager('customer')->save($account);

            return $this->jsonResponse([], Response::HTTP_ACCEPTED);
        }

        $currentTotal = ((float) $main->pricing_schema->price) + ((float) $extra->pricing_schema->price * $extra->quantity);
        $updateTotal = ((float) $main->pricing_schema->price) + ((float) $extra->pricing_schema->price * $quantity);

        $diffTotal = $updateTotal - $currentTotal;
        $dailyPrice = $diffTotal / 30;
        $invoicePrice = $dailyPrice * $days;

        if($request->get('confirm')){

            try{

                $data = [
                    'code' => md5(uniqid(time())),
                    'customer_id' => $subscription->customer->id,
                    'payment_method_code' => $subscription->payment_method->code,
                    'bill_items' => [[
                        'product_id' => $extra->product->id,
                        'amount' => round($invoicePrice, 2)
                    ]],
                    'metadata' => [
                        'extra_id' => $extra->id,
                        'count_users' => ($quantity + 1)
                    ]
                ];

                try {

                    $bill = $vindi->getBillService()->create($data);

                    if('paid' == $bill->status || Subscription::CODE_INVOICE == $subscription->payment_method->code){

                        /*$account->setMaxMember($quantity+1);
                        $this->manager('customer')->save($account);
                        $vindi->getProductItemService()->update($extra->id, [
                            'quantity' => $quantity
                        ]);*/

                        $message = 'Upgrade efetuado com sucesso!';

                        if(Subscription::CODE_INVOICE == $subscription->payment_method->code){
                            $message = 'Solicitação de upgrade efetuada com sucesso!';
                        }

                        return $this->jsonResponse([
                            'message' => $message
                        ]);

                    }else{

                        $charge = $bill->charges[0];
                        $transaction = $charge->last_transaction;

                        $vindi->getBillService()->delete($bill->id);

                        return $this->jsonResponse([
                            'message' => sprintf('%s - Código: %s', $transaction->gateway_message, $transaction->gateway_response_code)
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                }catch (RequestException $exception){

                    $errors = array_values($vindi->handleRequestErrors($exception));

                    return $this->jsonResponse([
                        'message' => $errors[0],
                        'data' => $exception->getErrors()
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }catch (RequestException $exception){
            }

            return $this->jsonResponse([
                'message' => 'Its upgrade'
            ], Response::HTTP_CONFLICT);
        }

        return $this->jsonResponse([
            'message' => 'upgrade',
            'content' =>  $this->renderView('signature.form_upgrade', [
                'quantity' => $quantity,
                'plan' => $subscription->plan,
                'users' => [
                    'old' => $extra->quantity + 1,
                    'new' => $quantity + 1
                ],
                'total' => [
                    'old' => $currentTotal,
                    'new' => $updateTotal
                ],
                'invoice' => $invoicePrice
            ])
        ], Response::HTTP_CONFLICT);
    }

    /**
     * @Route("/charge", name="signature_charge")
     * //@Method("post")
     */
    public function chargeAction(Request $request)
    {
        $id = $request->get('bill');
        $vindi = $this->getVindiHelper();
        $owner = $this->getCurrentAccount()->getOwner();

        $signature = $owner->getSignature();

        $subscription = $vindi->getSubscription($signature['subscription'])->getEntity();

        if (!$subscription) {

            return $this->jsonResponse([
                'message' => 'Assinatura não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        try {

            $bill = $vindi->getBillService()->get($id);

        }catch (RequestException $exception){

            return $this->jsonResponse([
                'message' => 'Fatura não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $charge = null;
        foreach ($bill->charges as $billCharge) {
            if ('pending' == $billCharge->status) {
                $charge = $billCharge;
                break;
            }
        }

        if (!$charge) {

            return $this->jsonResponse([
                'message' => 'Cobrança não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        try {

            $sendCharge = $vindi->getChargeService()->charge($charge->id, [
                'id' => $subscription->payment_profile->id
            ]);

            if ('paid' == $sendCharge->status) {

                return $this->jsonResponse([
                    'message' => 'Pagamento efetuado com sucesso!'
                ], Response::HTTP_ACCEPTED);
            }

            $transaction = $sendCharge->last_transaction;

            return $this->jsonResponse([
                'message' => sprintf('%s - Código: %s', $transaction->gateway_message, $transaction->gateway_response_code)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);


        } catch (RequestException $e) {

            $errors = array_values($vindi->handleRequestErrors($e));

            return $this->jsonResponse([
                'message' => $errors[0],
                'data' => $e->getErrors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    /**
     * @Route("/cancel", name="signature_cancel")
     * @Method("post")
     */
    public function cancelAction(Request $request)
    {
        $account = $this->getCurrentAccount();

        try {

            $this->getAccountManipulator()->deactivate($account);

            return $this->jsonResponse([], Response::HTTP_ACCEPTED);

        }catch (RequestException $exception) {

            $errors = array_values($this->getVindiHelper()->handleRequestErrors($exception));

            return $this->jsonResponse([
                'message' => $errors[0]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @return \AppBundle\Service\VindiHelper|object
     */
    private function getVindiHelper()
    {
        return $this->get('app.vindi_helper');
    }

    /**
     * @return array|mixed|string
     */
    private function getPlans()
    {
        return $this->getVindiHelper()->getPlans();
    }

    /**
     * @param Signature $signature
     * @return \Symfony\Component\Form\Form
     */
    private function createSignatureForm(Signature $signature)
    {
        return $this->createForm(SignatureType::class, $signature);
    }

    /**
     * @param $key
     * @param $value
     */
    private function addSignatureAttribute($key, $value)
    {
        $member = $this->getCurrentMember();

        $signature = $member->getSignature();

        $signature[$key] = $value;

        $member->addAttribute('signature', $signature);

        $this->manager('customer')->save($member);
    }

    /**
     * @return \AppBundle\Model\Signature\Common|null|object
     */
    private function getSubscription()
    {
        $subscription = null;
        $vindi = $this->getVindiHelper();
        $member = $this->getCurrentAccount()->getOwner();
        $signature = $member->getSignature();

        // Check current subscription
        if(null != $id = $signature['subscription']) {
            $subscription = $vindi->getSubscription($id);
        }

        return $subscription;
    }

    /**
     * @return \AppBundle\Service\Util\AccountManipulator|object
     */
    private function getAccountManipulator()
    {
        return $this->get('account_manipulator');
    }
}
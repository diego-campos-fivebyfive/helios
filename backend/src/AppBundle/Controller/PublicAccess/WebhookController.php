<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Configuration\App;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Model\Signature\Subscription;
use AppBundle\Service\VindiHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated
 *
 * @Route("/webhooks")
 */
class WebhookController extends AbstractController
{
    private $cronTokens = [
        'dev' => 'ciH7em6s115M3h8TIzu6bC5LunUpBODzxQ',
        'app' => 'WdG1eVppt0DlS_xhpIOaO6uhZDlm9PFimQ',
        'sandbox' => '98ZkOgnrBjlX_T9qawFH6bDAA_UfJgqRLQ'
    ];

    /**
     * @Route("/vindi", name="webhook_vindi")
     * @Method("post")
     */
    public function vindiAction(Request $request)
    {
        $handleTypes = ['bill_paid', 'charge_rejected', 'subscription_canceled'];

        $content = json_decode($request->getContent());

        /** @var \AppBundle\Entity\WebhookManager $webhookManager */
        $webhookManager = $this->get('app.webhook_manager');

        /** @var \AppBundle\Entity\Webhook $webhook */
        $webhook = $webhookManager->create();

        $webhook
            ->setContext('vindi')
            ->setType($content->event->type)
            ->setContent(json_encode($content));

        if(in_array($content->event->type, $handleTypes)) {

            switch ($content->event->type) {
                case 'bill_paid':

                    $bill = $content->event->data->bill;

                    if(null != $account = $this->getAccount($bill->customer->code)){
                        $this->getAccountManipulator()->paymentConfirmed($account, $bill);
                    }

                    $webhook->setSubscriptionId($bill->subscription->id);

                    break;

                case 'charge_rejected':

                    $charge = $content->event->data->charge;

                    if(null == $charge->bill->code) {
                        if (null != $account = $this->getAccount($charge->customer->code)) {
                            if (!$account->getExpireAt()) {

                                $date = new \DateTime($charge->created_at);
                                $this->get('account_manipulator')->paymentRejected($account, $date);

                                if(Subscription::CODE_CREDIT_CARD == $charge->payment_method->code) {

                                    $signature = $account->getSignature();

                                    if(null != $signature['subscription']) {
                                        $this->getMailer()->sendUnprocessedPaymentRenewSignature($account->getOwner());
                                    }
                                }
                            }
                        }
                    }

                    break;

                case 'subscription_canceled':

                    $subscription = $content->event->data->subscription;

                    if(null != $account = $this->getAccount($subscription->customer->code)){
                        if(!$account->getExpireAt()) {
                            $this->get('account_manipulator')->deactivate($account);
                        }
                    }

                    break;
            }
        }

        $webhookManager->save($webhook);

        return $this->jsonResponse([
            'webhook' => $webhook->getToken()
        ]);
    }

    /**
     * @Route("/check_bills", name="webhook_check_bills")
     * @Method("post")
     */
    public function checkBillsAction()
    {
        /** @var \AppBundle\Service\VindiHelper $vindi */
        $vindi = $this->get('app.vindi_helper');

        $dueAt = (new \DateTime(App::BILLS_PENDING_AFTER))->format('Y-m-d');

        // Pending Query
        $pendingQuery = sprintf('(payment_method_id:17908 AND status=pending AND due_at=%s)', $dueAt);

        $pendingBills = $vindi->getBillService()->all([
            'query' => $pendingQuery
        ]);

        // Pending
        $pendingIds = [];
        if(!empty($pendingBills)){
            foreach ($pendingBills as $pendingBill){
                $pendingIds[] = $pendingBill->id;
                $vindi->getSubscriptionService()->delete($pendingBill->subscription->id);
            }
        }

        $paidIds = [];
        
        // Paid Query
        /*$paidQuery = sprintf('(payment_method_id:17908 AND status=paid AND due_at>=%s)', $dueAt);
        $paidBills = $vindi->getBillService()->all([
            'query' => $paidQuery
        ]);

        if(!empty($paidBills)){
            foreach ($paidBills as $paidBill){
                if(null != $account = $this->getAccount($paidBill->customer->code)){
                    $paidBills[] = $paidBill->id;
                    $this->getAccountManipulator()->paymentConfirmed($account, $paidBill);
                }
            }
        }*/

        return $this->jsonResponse([
            'pending_bills' => $pendingIds,
            'paid_bills' => $paidIds
        ]);
    }


    /**
     * @Route("/reset_projects_count")
     * @Method("post")
     */
    public function resetProjectsCountAction(Request $request)
    {
        $env = $request->request->get('env');

        if(!array_key_exists($env, $this->cronTokens)){
            return $this->jsonResponse(['unauthorized_env' => $request->request->all()]);
        }

        $token = $request->request->get('token');

        if($token !== $this->cronTokens[$env]){
            return $this->jsonResponse(['unauthorized_token' => $request->request->all()]);
        }

        $manager = $this->manager('customer');

        $accounts = $manager->findBy([
            'context' => BusinessInterface::CONTEXT_ACCOUNT
        ]);

        $mode = $request->get('mode', 'real');

        $resets = [
            'mode' => $mode,
            'accounts' => []
        ];

        foreach ($accounts as $key => $account){
            if($account instanceof AccountInterface || $account instanceof BusinessInterface) {

                $resets['accounts'][] = [
                    'id' => $account->getId(),
                    'email' => $account->getEmail(),
                    'projects_count' => $account->getProjectsCount()
                ];

                if('real' == $mode) {
                    $account->setAttribute(AccountInterface::ATTR_PROJECTS_COUNT, 0);
                    $manager->save($account, $key == (count($accounts) - 1));
                }
            }
        }

        return $this->jsonResponse($resets);
    }

    /**
     * Find account by owner token reference
     * @param $token
     */
    private function getAccount($token)
    {
        $member = $this->manager('customer')->findOneBy([
            'token' => $token
        ]);

        if($member instanceof BusinessInterface){
            return $member->getAccount();
        }

        return null;
    }

    /**
     * @return \AppBundle\Service\Util\AccountManipulator|object
     */
    private function getAccountManipulator()
    {
        return $this->get('account_manipulator');
    }

    /**
     * @return \AppBundle\Service\Mailer|object
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
    }
}
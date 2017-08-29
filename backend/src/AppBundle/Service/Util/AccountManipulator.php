<?php

namespace AppBundle\Service\Util;

use AppBundle\Configuration\App;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountManipulator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $name
     * @param $email
     * @return BusinessInterface
     */
    public function create($name, $email)
    {
        $manager = $this->getAccountManager();
        $context = $this->getContextManager()->find(Customer::CONTEXT_ACCOUNT);

        /** @var \AppBundle\Entity\BusinessInterface $account */
        $account = $manager->create();

        $account
            ->setContext($context)
            ->setFirstname($name)
            ->setEmail($email);

        $manager->save($account);

        return $account;
    }

    /**
     * @param $account BusinessInterface|string
     */
    public function activate($account)
    {
        $account = $this->checkAccount($account);

        $account
            ->setExpireAt(null)
            ->setStatus(BusinessInterface::STATUS_ENABLED);

        $this->getAccountManager()->save($account);
    }

    /**
     * @param $account BusinessInterface|string
     */
    public function deactivate($account)
    {
        /** @var BusinessInterface $account */
        $account = $this->checkAccount($account);
        $manager = $this->getAccountManager();

        foreach ($account->getMembers() as $member){
            if($member instanceof BusinessInterface && !$member->isMasterOwner()){
                $manager->delete($member);
            }
        }

        $account
            //->setExpireAt(null)
            ->setStatus(BusinessInterface::STATUS_LOCKED);

        $manager->delete($account);

        $owner = $account->getOwner();
        $signature = $owner->getSignature();

        if(null != $id = $signature['subscription']){

            $this->getVindiHelper()->cancelSubscription($id);

            $signature['subscription'] = null;
            $owner->setAttribute('signature', $signature);

            $manager->save($owner);
        }
    }

    /**
     * @param $account
     * @param \DateTime|null $date
     */
    public function paymentRejected($account, \DateTime $date = null)
    {
        /** @var BusinessInterface $account */
        $account = $this->checkAccount($account);

        if(!$date){
            $date = new \DateTime();
        }

        $interval = date_interval_create_from_date_string(App::PAYMENT_REJECTED_INTERVAL);

        $date->add($interval);

        $account->setExpireAt($date);

        $this->getAccountManager()->save($account);
    }

    /**
     * @param \ $account
     * @param $invoice
     */
    public function paymentConfirmed($account, $invoice)
    {
        /** @var BusinessInterface $account */
        $account = $this->checkAccount($account);

        // Upgrade
        if(null != $invoice->code){

            $metadata = $invoice->metadata;

            $countUsers = $metadata->count_users;

            $this->getVindiHelper()->getProductItemService()->update($metadata->extra_id, [
                'quantity' => ($metadata->count_users - 1)
            ]);

        // Create
        }else{

            $item = $invoice->bill_items[1];
            $countUsers = ($item->quantity+1);
        }

        $account
            ->setExpireAt(null)
            ->setStatus(BusinessInterface::STATUS_ENABLED)
            ->setMaxMember($countUsers);

        $this->getAccountManager()->save($account);
    }

    /**
     * @param $account
     * @return null|object
     * @throws \Exception
     */
    private function checkAccount($account)
    {
        $manager = $this->getAccountManager();

        if(is_string($account)){
            $account = $manager->findOneBy([
                'email' => $account,
                'context' => BusinessInterface::CONTEXT_ACCOUNT
            ]);
        }

        if(!$account){
            throw new \Exception('Account not found');
        }

        if($account instanceof BusinessInterface && !$account->isAccount()){
            throw new \InvalidArgumentException('Invalid account context');
        }

        return $account;
    }

    /**
     * @return object|\AppBundle\Manager\CustomerManager
     */
    private function getAccountManager()
    {
        return $this->container->get('customer_manager');
    }

    /**
     * @return object|\Sonata\ClassificationBundle\Entity\ContextManager
     */
    private function getContextManager()
    {
        return $this->container->get('sonata.classification.manager.context');
    }

    /**
     * @return \AppBundle\Service\VindiHelper|object
     */
    private function getVindiHelper()
    {
        return $this->container->get('app.vindi_helper');
    }
}
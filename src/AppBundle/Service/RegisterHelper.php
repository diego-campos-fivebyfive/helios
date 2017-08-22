<?php

namespace AppBundle\Service;

use AppBundle\Configuration\App;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Extra\AccountRegister;
use AppBundle\Entity\Package;
use AppBundle\Entity\UserInterface;
use AppBundle\Model\KitPricing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegisterHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param AccountInterface $account
     */
    public function finishAccountRegister(AccountInterface $account, $save = true)
    {
        $owner = $account->getOwner();

        $this->createAccountDefaults($account);

        $account->setConfirmationToken(null);
        $owner->setConfirmationToken(null);

        if($save){
            $this->container->get('account_manager')->save($account);
        }
    }

    /**
     * @param BusinessInterface $member
     */
    public function finishMemberRegistration(BusinessInterface &$member)
    {
        $user = $member->getUser();
        $account = $member->getAccount();
        $countMembers = $account->getMembers()->count();

        if(1 == $countMembers || $member->getAttribute('is_owner')){
            $user->addRole(UserInterface::ROLE_OWNER);
            if(1 == $countMembers){
                $user->addRole(UserInterface::ROLE_OWNER_MASTER);
            }
        }else{
            //$this->resolveMemberDependencies($member);
        }

        $manager = $this->getCustomerManager();

        $member
            ->setTimezone('America/Sao_Paulo')
            ->setConfirmationToken(null)
            ->setTeam($this->getDefaultTeam($account));

        $manager->save($member);

        $account->setConfirmationToken(null);
        $manager->save($account);
    }

    /**
     * @param BusinessInterface $account
     * @return \AppBundle\Entity\TeamInterface
     */
    public function createDefaultAccountTeam(BusinessInterface $account)
    {
        $manager = $this->getTeamManager();

        /** @var \AppBundle\Entity\TeamInterface $team */
        $team = $manager->create();
        $team->setAccount($account)
            ->setEnabled(1)
            ->setName('Equipe 1')
            ->setDescription('Equipe 1')
        ;

        $manager->save($team);

        return $team;
    }

    /**
     * @param BusinessInterface $account
     */
    public function createDefaultKitMargins(BusinessInterface $account)
    {
        $fields = ['name', 'target', 'percent'];

        $margins = [
            [ 'Lucro bruto',                     KitPricing::TARGET_GENERAL,     20 ],
            [ 'Imposto simples equipamentos',    KitPricing::TARGET_EQUIPMENTS,  4  ],
            [ 'Imposto simples serviço',         KitPricing::TARGET_SERVICES,    6  ],
            [ 'Comissão',                        KitPricing::TARGET_GENERAL,     5  ]
        ];

        $manager = $this->getKitPricingManager();
        $manager->setAccount($account);

        foreach ($margins as $margin){

            $pricing = $manager->create(array_combine($fields, $margin));

            $manager->save($pricing);
        }
    }

    /**
     * @param AccountInterface $account
     */
    public function createDefaultAccountCategories(AccountInterface $account)
    {
        $classifications = [
            CategoryInterface::CONTEXT_CONTACT => ['Cliente Residencial', 'Cliente Empresarial', 'Fornecedor', 'Parceiro'],
            CategoryInterface::CONTEXT_SALE_STAGE => ['Proposta Emitida', 'Proposta Apresentada', 'Negociação', 'Projeto Cancelado', 'Projeto Vendido']
        ];

        $categoryManager = $this->getCategoryManager();
        $contextManager = $this->getContextManager();

        $index = count($classifications);
        $count = 1;
        foreach($classifications as $contextId => $names){
            $context = $contextManager->find($contextId);
            if($context){
                foreach($names as $key => $name){
                    $category = $categoryManager->create();
                    $category->setAccount($account);
                    $category->setContext($context);
                    $category->setName($name);
                    $category->setPosition($key+1);
                    $categoryManager->save($category,($count == $index));
                }
            }
            $count++;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailCanBeUsed($email)
    {
        if($this->emailCanBeUsedForUser($email)){
            if($this->emailCanBeUsedForMember($email)){
                if($this->emailCanBeUsedForAccount($email)){
                    return $this->emailCanBeUsedForRegister($email);
                }
            }
        }

        return false;
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailCanBeUsedForUser($email)
    {
        return null == $this->getUserManager()->findUserByUsernameOrEmail($email);
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailCanBeUsedForMember($email)
    {
        return null == $this->findBusinessByEmail($email, BusinessInterface::CONTEXT_MEMBER);
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailCanBeUsedForAccount($email)
    {
        return null == $this->findBusinessByEmail($email, BusinessInterface::CONTEXT_ACCOUNT);
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailCanBeUsedForRegister($email)
    {
        $register = $this->findRegisterByEmail($email);

        if($register instanceof AccountRegister){
            return !$register->isDone();
        }

        return true;
    }

    /**
     * @param $email
     * @return null|object
     */
    public function findRegisterByEmail($email)
    {
        return $this->getAccountRegisterManager()->findOneBy([
            'email' => $email
        ]);
    }

    /**
     * @param $email
     * @param $context
     * @return null|object
     */
    public function findBusinessByEmail($email, $context)
    {
        return $this->getCustomerManager()->findOneBy([
            'email' => $email,
            'context' => $context
        ]);
    }

    /**
     * @param AccountRegister $register
     * @return \AppBundle\Entity\BusinessInterface
     */
    private function transformRegisterIntoAccount(AccountRegister $register)
    {
        $context =  $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);
        $manager = $this->getCustomerManager();

        /** @var \AppBundle\Entity\BusinessInterface $account */
        $account = $manager->create();
        $account
            ->setConfirmationToken($register->getConfirmationToken())
            ->setContext($context)
            ->setFirstname($register->getCompanyName())
            ->setEmail($register->getEmail())
            ->setPhone($register->getPhone())
        ;

        $companyAttributes = [
            'companyName' => $register->getCompanyName(),
            'companyStatus' => $register->getCompanyStatus(),
            'companySector' => $register->getCompanySector(),
            'companyMembers' => $register->getCompanyMembers()
        ];

        $account->setAttributes($companyAttributes);

        if(null != $package = $this->getDefaultPackage()){
            //$account->setPackage($package);
        }

        //$account->setExpireAt(new \DateTime(App::TRAIL_INTERVAL));

        $manager->save($account);

        $this->createAccountDefaults($account);

        return $account;
    }

    /**
     * @param AccountRegister $register
     * @return \AppBundle\Entity\BusinessInterface
     */
    private function transformRegisterIntoMember(AccountRegister $register)
    {
        $context =  $this->getContextManager()->find(BusinessInterface::CONTEXT_MEMBER);
        $account = $this->transformRegisterIntoAccount($register);

        $manager = $this->getCustomerManager();

        /** @var \AppBundle\Entity\BusinessInterface $member */
        $member = $manager->create();
        $member
            ->setContext($context)
            ->setFirstname($register->getName())
            ->setEmail($register->getEmail())
            ->setPhone($register->getPhone())
            ->setConfirmationToken($this->getTokenGenerator()->generateToken())
            ->setAccount($account)
        ;

        $manager->save($member);

        return $member;
    }

    /**
     * @return null|\AppBundle\Entity\Package
     */
    private function getDefaultPackage()
    {
        return $this->getPackageManager()->findOneBy([
            'status' => Package::ENABLED,
            'default' => true
        ]);
    }

    /**
     * @param BusinessInterface $account
     * @return \AppBundle\Entity\TeamInterface|null|object
     */
    private function getDefaultTeam(BusinessInterface $account)
    {
        if(null == $team = $this->getTeamManager()->findOneBy(['account' => $account])){
            $team = $this->createDefaultAccountTeam($account);
        }

        return $team;
    }

    /**
     * @param AccountInterface $account
     */
    public function createAccountDefaults(AccountInterface $account)
    {
        $this->createDefaultAccountCategories($account);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManager|object
     */
    private function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }

    /**
     * @return \AppBundle\Entity\CustomerManager|object
     */
    private function getCustomerManager()
    {
        return $this->container->get('app.customer_manager');
    }

    /**
     * @return \AppBundle\Entity\Extra\AccountRegisterManager|object
     */
    private function getAccountRegisterManager()
    {
        return $this->container->get('app.account_register_manager');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\ContextManager|object
     */
    private function getContextManager()
    {
        return $this->container->get('sonata.classification.manager.context');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\CategoryManager|object
     */
    private function getCategoryManager()
    {
        return $this->container->get('sonata.classification.manager.category');
    }

    /**
     * @return \AppBundle\Entity\TeamManager|object
     */
    private function getTeamManager()
    {
        return $this->container->get('app.team_manager');
    }

    /**
     * @return \AppBundle\Entity\PackageManager|object
     */
    private function getPackageManager()
    {
        return $this->container->get('app.package_manager');
    }

    /**
     * @return \AppBundle\Entity\Component\PricingManager|object
     */
    private function getKitPricingManager()
    {
        return $this->container->get('app.kit_pricing_manager');
    }

    /**
     * @return \FOS\UserBundle\Util\TokenGenerator|object
     */
    private function getTokenGenerator()
    {
        return $this->container->get('fos_user.util.token_generator');
    }
}
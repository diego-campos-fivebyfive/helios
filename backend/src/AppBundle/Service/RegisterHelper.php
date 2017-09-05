<?php

namespace AppBundle\Service;

use AppBundle\Configuration\App;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Package;
use AppBundle\Entity\UserInterface;
use AppBundle\Model\KitPricing;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
     * @param array $data
     */
    public function fillAccount(AccountInterface $account, array $data)
    {
        unset($data['contact']);

        $data['country'] = 'BR';
        $data['level'] = 'partner';
        $data['context'] = BusinessInterface::CONTEXT_ACCOUNT;

        $this->fillObject($account, $data);
    }

    /**
     * @param AccountInterface $account
     */
    public function finishAccountRegister(AccountInterface $account, $save = true)
    {
        $owner = $account->getOwner();

        $this->createAccountDefaults($account);

        $account->setConfirmationToken(null);

        $owner
            ->setTimezone('America/Sao_Paulo')
            ->setConfirmationToken(null);

        if($save){
            $this->container->get('account_manager')->save($account);
        }
    }

    /**
     * @param MemberInterface $member
     * @param array $data
     */
    public function fillMember(MemberInterface $member, array $data)
    {
        $data = array_merge($data, [
            'firstname' => $data['contact'],
            'context' => BusinessInterface::CONTEXT_MEMBER,
            'confirmationToken' => $this->getTokenGenerator()->generateToken()
        ]);

        unset($data['document'], $data['contact']);
        ksort($data); // Prevent context exception

        $this->fillObject($member, $data);
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
            return $this->emailCanBeUsedForMemberOrAccount($email);
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
    public function emailCanBeUsedForMemberOrAccount($email)
    {
        return null == $this->getCustomerManager()->findOneBy(['email' => $email]);
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
     * @param AccountInterface $account
     */
    public function createAccountDefaults(AccountInterface $account)
    {
        $this->createDefaultAccountCategories($account);
    }

    /**
     * @param $object
     * @param array $data
     */
    private function fillObject($object, array $data)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $property => $value) {
            $accessor->setValue($object, $property, $value);
        }
    }

    /**
     * @return \FOS\UserBundle\Model\UserManager|object
     */
    private function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }

    /**
     * @return \AppBundle\Manager\CustomerManager|object
     */
    private function getCustomerManager()
    {
        return $this->container->get('customer_manager');
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
<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\UserInterface;
use Tests\AppBundle\AppTestCase;
use AppBundle\Entity\MemberInterface;
use AppBundle\Service\Util\AccountTransfer;

/**
 * Class AccountTransferTest
 * @group account_transfer
 */
class AccountTransferTest extends AppTestCase
{
    public function testAccountTransfer()
    {
        $container = $this->getContainer();

        $source = $this->createUser();
        $target = $this->createUser2();

        $account1 = $this->createAccount1();
        $account2 = $this->createAccount2();
        $account3 = $this->createAccount3();
        $account4 = $this->createAccount4();
        $account5 = $this->createAccount5();
        $account6 = $this->createAccount6();
        $account7 = $this->createAccount7();
        $account8 = $this->createAccount8();
        $account9 = $this->createAccount9();
        $account10 = $this->createAccount10();

        /** @var \AppBundle\Service\Util\AccountTransfer $transfer */
        $accountTransfer = $container->get('account_transfer');

        $accountTransfer->transfer($source, $target);

        $this->assertInstanceOf(MemberInterface::class, $source);
        $this->assertNotNull($source->getId());
    }

    private function createUser()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');
        $manager = $this->manager('member');

        $user = $userManager->createUser();

        $user->setEmail('joao@joao.com')
            ->setUsername('joao@joao.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_PLATFORM_COMMERCIAL
            ]);

        $member = $manager->create();
        $member->setFirstname('joao');
        $member->setEmail('joao@joao.com');
        $member->setContext(BusinessInterface::CONTEXT_MEMBER);
        $member->setUser($user);

        $manager->save($member);

        return $member;
    }

    private function createUser2()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');
        $manager = $this->manager('member');

        $user = $userManager->createUser();

        $user->setEmail('pedro@pedro.com')
            ->setUsername('pedro@pedro.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_PLATFORM_COMMERCIAL
            ]);

        $member2 = $manager->create();
        $member2->setFirstname('pedro');
        $member2->setEmail('pedro@pedro.com');
        $member2->setContext(BusinessInterface::CONTEXT_MEMBER);
        $member2->setUser($user);

        $manager->save($member2);

        return $member2;
    }

    public function createAccount1()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'joao@joao.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('eloa@eloa.com')
            ->setUsername('eloa@eloa.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('82.316.162/0001-06')
            ->setExtraDocument('440.67641-39')
            ->setFirstName('Eloá e Evelyn Entulhos')
            ->setLastName('Eloá e Evelyn Entulhos Ltda')
            ->setPostcode('81490-676')
            ->setState('PR')
            ->setCity('Curitiba')
            ->setDistrict('Campo de Santana')
            ->setStreet('Rua Osmar Benedito Aleluia')
            ->setNumber('215')
            ->setEmail('orcamento@eloaeevelynentulhosltda.com.br')
            ->setPhone('(41) 3659-8293')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Eloá')
            ->setEmail('eloa@eloa.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount2()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'joao@joao.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('andre@andre.com')
            ->setUsername('andre@andre.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('87.945.600/0001-00')
            ->setExtraDocument('952.29416-43')
            ->setFirstName('André e Fernanda Telecom')
            ->setLastName('André e Fernanda Telecom ME')
            ->setPostcode('86604-506')
            ->setState('PR')
            ->setCity('Rolândia')
            ->setDistrict('Jardim Novo Horizonte')
            ->setStreet('Rua Wilson Carvalho de Oliveira')
            ->setNumber('577')
            ->setEmail('financeiro@andreefernandatelecomme.com.br')
            ->setPhone('(43) 2788-1921')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('André')
            ->setEmail('andre@andre.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount3()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'joao@joao.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('bernardo@bernardo.com')
            ->setUsername('bernardo@bernardo.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('40.149.715/0001-25')
            ->setExtraDocument('640.10215-21')
            ->setFirstName('Bernardo e Vitor Publicidade')
            ->setLastName('Bernardo e Vitor Publicidade e Propaganda Ltda')
            ->setPostcode('83040-380')
            ->setState('PR')
            ->setCity('São José dos Pinhais')
            ->setDistrict('Afonso Pena')
            ->setStreet('Rua Rosália Stanczyk')
            ->setNumber('818')
            ->setEmail('diretoria@bernardoevitorpublicidadeepropagandaltda.com.br')
            ->setPhone('(41) 2754-2413')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Bernardo')
            ->setEmail('bernardo@bernardo.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount4()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'joao@joao.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('fernando@fernando.com')
            ->setUsername('fernando@fernando.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('77.066.873/0001-21')
            ->setExtraDocument('035.51110-59')
            ->setFirstName('Fernando e Clara Doces & Salgados')
            ->setLastName('Fernando e Clara Doces & Salgados ME')
            ->setPostcode('86604-300')
            ->setState('PR')
            ->setCity('Rolândia')
            ->setDistrict('Jardim Novo Horizonte')
            ->setStreet('Rua dos Piriquitos')
            ->setNumber('476')
            ->setEmail('sac@fernandoeclaradocessalgadosme.com.br')
            ->setPhone('(43) 3771-0410')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Fernando')
            ->setEmail('fernando@fernando.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount5()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'joao@joao.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('iago@iago.com')
            ->setUsername('iago@iago.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('34.292.287/0001-92')
            ->setExtraDocument('694.17699-24')
            ->setFirstName('Iago e Bruno Construções')
            ->setLastName('Iago e Bruno Construções ME')
            ->setPostcode('87504-760')
            ->setState('PR')
            ->setCity('Umuarama')
            ->setDistrict('Jardim Laguna')
            ->setStreet('Rua Jaime Gazzi')
            ->setNumber('337')
            ->setEmail('ti@iagoebrunoconstrucoesme.com.br')
            ->setPhone('(44) 3937-0212')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Iago')
            ->setEmail('iago@iago.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount6()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'pedro@pedro.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('isaac@isaac.com')
            ->setUsername('isaac@isaac.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('25.902.756/0001-57')
            ->setExtraDocument('615.08795-56')
            ->setFirstName('Isaac e Bárbara Limpeza')
            ->setLastName('Isaac e Bárbara Limpeza Ltda')
            ->setPostcode('83602-604')
            ->setState('PR')
            ->setCity('Campo Largo')
            ->setDistrict('Botiatuva')
            ->setStreet('Rua Pedro Campagnaro')
            ->setNumber('856')
            ->setEmail('tesouraria@isaacebarbaralimpezaltda.com.br')
            ->setPhone('(41) 2726-0765')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Isaac')
            ->setEmail('isaac@isaac.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount7()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'pedro@pedro.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('mariana@mariana.com')
            ->setUsername('mariana@mariana.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('01.085.060/0001-17')
            ->setExtraDocument('194.68382-05')
            ->setFirstName('Mariana e Raquel Mudanças')
            ->setLastName('Mariana e Raquel Mudanças Ltda')
            ->setPostcode('86080-555')
            ->setState('PR')
            ->setCity('Londrina')
            ->setDistrict('Conjunto Habitacional José Garcia Molina')
            ->setStreet('Rua Leonídia Pereira de Oliveira')
            ->setNumber('495')
            ->setEmail('auditoria@marianaeraquelmudancasltda.com.br')
            ->setPhone('(43) 3788-8216')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Mariana')
            ->setEmail('mariana@mariana.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount8()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'pedro@pedro.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('pietro@pietro.com')
            ->setUsername('pietro@pietro.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('04.700.141/0001-78')
            ->setExtraDocument('139.31774-26')
            ->setFirstName('Pietro e Stella Alimentos')
            ->setLastName('Pietro e Stella Alimentos Ltda')
            ->setPostcode('85602-226')
            ->setState('PR')
            ->setCity('Francisco Beltrão')
            ->setDistrict('Cristo Rei')
            ->setStreet('Rua Eduardo Faust')
            ->setNumber('427')
            ->setEmail('treinamento@pietroestellaalimentosltda.com.br')
            ->setPhone('(46) 2723-4530')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Pietro')
            ->setEmail('pietro@pietro.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount9()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'pedro@pedro.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('debora@debora.com')
            ->setUsername('debora@debora.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('89.487.845/0001-67')
            ->setExtraDocument('357.06394-97')
            ->setFirstName('Débora e Marina Assessoria Jurídica')
            ->setLastName('Débora e Marina Assessoria Jurídica Ltda')
            ->setPostcode('86047-130')
            ->setState('PR')
            ->setCity('Londrina')
            ->setDistrict('Tucano')
            ->setStreet('Rua Vila-Lobos')
            ->setNumber('693')
            ->setEmail('contato@deboraemarinaassessoriajuridicaltda.com.br')
            ->setPhone('(43) 2839-9223')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Debora')
            ->setEmail('debora@debora.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }

    public function createAccount10()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $account2Manager = $container->get('account_manager');
        $member1 = $account2Manager->findOneBy([
            'context' => 'member',
            'email' => 'pedro@pedro.com'
        ]);

        $user = $userManager->createUser();
        $user->setEmail('antonia@antonia.com')
            ->setUsername('antonia@antonia.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER_MASTER
            ]);

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account
            ->setDocument('58.521.379/0001-65')
            ->setExtraDocument('704.27847-07')
            ->setFirstName('Antonio e Antonella Doces & Salgados')
            ->setLastName('Antonio e Antonella Doces & Salgados Ltda')
            ->setPostcode('86037-789')
            ->setState('PR')
            ->setCity('Londrina')
            ->setDistrict('Residencial Abussafe')
            ->setStreet('Rua Orlando Sisti')
            ->setNumber('378')
            ->setEmail('contabilidade@antonioeantonelladocessalgadosltda.com.br')
            ->setPhone('(43) 2526-9055')
            ->setStatus(2)
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setAgent($member1)
            ->setLevel('partner');

        /** @var MemberInterface $member */
        $member = $accountManager->create();
        $member
            ->setFirstname('Antonio')
            ->setEmail('antonio@antonio.com')
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setUser($user)
            ->setAccount($account);

        $accountManager->save($account);
    }
}

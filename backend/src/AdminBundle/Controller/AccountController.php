<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Account\TransferType;
use AdminBundle\Form\AccountType;
use AdminBundle\Form\EmailMemberType;
use AdminBundle\Form\MemberType;
use AppBundle\Configuration\Brazil;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Util\AccountTransfer;
use Symfony\Component\Form\FormError;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_AFTER_SALES') or has_role('ROLE_PLATFORM_EXPANSE')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Accounts", route={"name"="account_index"})
 *
 * @Route("account")
 */
class AccountController extends AdminController
{
    /**
     * @Route("/", name="account_index")
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->getPaginator();

        $manager = $this->manager('customer');

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from(Customer::class, 'a')
            ->where('a.context = :context')
            ->setParameters([
                'context' => $this->getAccountContext()
            ]);

        if(-1 != $state = $request->get('state', -1)){
            $qb->andWhere('a.state = :state');
            $qb->setParameter('state', $state);
        }

        if(-1 != $bond = $request->get('bond', -1)){
            $qb->andWhere('a.agent = :bond');
            $qb->setParameter('bond', $bond);
        }

        if(-1 != $status = $request->get('status', -1)){
            $qb->andWhere('a.status = :status');
            $qb->setParameter('status', $status);
        }

        if(-1 != $level = $request->get('level', -1)) {
            $qb->andWhere('a.level = :level');
            $qb->setParameter('level', $level);
        }

        $expanseStates = [];
        if ($this->member()->isPlatformExpanse()) {

            $expanseStates = $this->member()->getAttributes()['states'];

            $qb->andWhere($qb->expr()->in('a.state', $expanseStates));
        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        /** @var MemberInterface $member */
        $members = $manager->findBy([
            'context' => MemberInterface::CONTEXT,
            'account' => $this->account()
        ]);

        $membersSices = [];
        foreach ($members as $i => $member) {
            if($member->getUser()->isEnabled() && $member->isPlatformCommercial()) {
                $membersSices[$i] = $member;
            }
        }

        $levels = Memorial::getDefaultLevels();

        unset($levels[Memorial::LEVEL_PROMOTIONAL]);

        $states = $this->getStates($expanseStates);

        return $this->render('admin/accounts/index.html.twig', array(
            'current_state' => $state,
            'current_status' => $status,
            'allStatus' =>Customer::getStatusList(),
            'current_level' => $level,
            'allLevels' => $levels,
            'current_bond' => $bond,
            'members' => $membersSices,
            'states' => $states,
            'pagination' => $pagination
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Breadcrumb("New Account")
     * @Route("/create", name="account_create")
     */
    public function createAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $accountManager = $this->manager('customer');
        $memberManager = $this->manager('customer');

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        $account->setContext(Customer::CONTEXT_ACCOUNT);

        if ($this->member()->isPlatformCommercial()) {
            $account->setAgent($this->member());
        }

        /** @var MemberInterface $member */
        $member = $memberManager->create();
        $member->setContext(Customer::CONTEXT_MEMBER);

        $account->addMember($member);

        $form = $this->createForm(AccountType::class, $account, array(
            'method' => 'post',
            'member' => $this->member()
        ));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $helper = $this->getRegisterHelper();

            $account->setConfirmationToken($this->getTokenGenerator()->generateToken());

            $member->setEmail($account->getEmail());

            $document = $accountManager->findBy([
                'document' => $account->getDocument()
            ]);

            if (!$this->member()->isPlatformAdmin() || !$this->member()->isPlatformMaster()) {
                $account->setLevel(Memorial::LEVEL_PARTNER);
            }

            $existsDoc = false;

            if($document) {
                $form->addError(new FormError('CNPJ já Cadastrado'));
                $existsDoc = true;
            }

            if ($helper->emailCanBeUsed($account->getEmail()) && !$existsDoc) {
                $user = $userManager->createUser();

                $user
                    ->setEmail($member->getEmail())
                    ->setUsername($member->getEmail())
                    ->setPlainPassword(uniqid())
                    ->setRoles([
                        UserInterface::ROLE_OWNER,
                        UserInterface::ROLE_OWNER_MASTER
                    ]);

                $member->setUser($user);

                $account->setStatus(Customer::APROVED);

                $accountManager->save($account);

                $this->setNotice("Conta criada com sucesso !");

                if ($account->isAproved()) {
                    $this->getMailer()->sendAccountConfirmationMessage($account);
                }

                return $this->redirectToRoute('account_show', [
                    'id' => $account->getId()
                ]);
            }

            $form->addError(new FormError('E-mail já Cadastrado'));

        }

        return $this->render('admin/accounts/form.html.twig', array(
            'errors' => $form->getErrors(true),
            'form' => $form->createView(),
            'account' => $account
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Breadcrumb("update.account")
     * @Route("/{id}/update", name="account_update")
     */
    public function updateAction(Request $request, Customer $account)
    {

        $manager = $this->manager('customer');

        $email = $account->getEmail();

        $form = $this->createForm(AccountType::class, $account, array(
            'method' => 'post',
            'member' => $this->member()
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if ($this->member()->isPlatformCommercial() && !$account->getAgent()) {
                $account->setAgent($this->member());
            }

            $helper = $this->getRegisterHelper();
            $masterOwner = $account->getOwner();
            $newEmail = $account->getEmail();

            if (!$helper->emailCanBeUsed($newEmail) && $masterOwner->getEmail() == $newEmail
                || $helper->emailCanBeUsed($newEmail)) {

                $manager->save($account);

                $masterOwner->setEmail($newEmail);
                $manager->save($masterOwner);

                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $user = $masterOwner->getUser();
                $user->setEmail($newEmail);
                $user->setEmailCanonical($newEmail);
                $user->setUsername($newEmail);
                $user->setUsernameCanonical($newEmail);

                $userManager->updateUser($user);

                $this->setNotice("Conta atualizada com sucesso!");

                return $this->redirectToRoute('account_show', [
                    'id' => $account->getId()
                ]);

            } else {

                $form->addError(new FormError('Este email não pode ser usado'));

            }
        }

        return $this->render('admin/accounts/form.html.twig', [
            'errors' => $form->getErrors(true),
            'form' => $form->createView(),
            'account' => $account
        ]);
    }

    /**
     * @Route("/transfer", name="account_transfer")
     */
    public function transferAction(Request $request)
    {
        $form = $this->createForm(TransferType::class, null, [
            'account' => $this->account()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var \AppBundle\Service\Util\AccountTransfer $transfer */
            $transfer = $this->get('account_transfer');

            $transfer->transfer($form->getData()['source'], $form->getData()['target']);

            return $this->json([]);
        }

        return $this->render('admin/accounts/transfer.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{token}/change", name="account_change_status")
     * @Method("post")
     */
    public function changeAction(Customer $account)
    {
        try {
            switch ($account->getStatus()) {
                case BusinessInterface::PENDING:
                    $this->getMailer()->sendAccountVerifyMessage($account);
                    break;
                case BusinessInterface::STANDING:
                    $account = $this->changeStatus($account, BusinessInterface::APROVED);
                    $account->setAgent($this->member());
                    $this->getMailer()->sendAccountConfirmationMessage($account);
                    break;
                case BusinessInterface::APROVED:
                    $this->getMailer()->sendAccountConfirmationMessage($account);
                    break;
                case BusinessInterface::ACTIVATED:
                    $this->changeStatus($account, BusinessInterface::LOCKED);
                    break;
                case BusinessInterface::LOCKED:
                    foreach ($account->getMembers() as $member){
                        $member->getUser()->setEnabled(1);
                    }
                    $this->changeStatus($account, BusinessInterface::ACTIVATED);
                    break;
            }

            $this->manager('customer')->save($account);

            $status = Response::HTTP_OK;
        } catch (\Exception $exception) {
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json([
            'info_status' => $this->renderView('admin/accounts/info_status.html.twig', ['account' => $account])
        ], $status);
    }

    /**
     * @param Customer $account
     * @param $status
     * @return Customer
     */
    public function changeStatus(Customer $account, $status)
    {
        $member = $account->getOwner();

        $member->setStatus($status);
        $account->setStatus($status);

        $this->manager('account')->save($account);

        return $account;
    }

    /**
     * @Route("/{id}", name="account_show")
     */
    public function showAction(Request $request, Customer $account)
    {
        $member = $account->getMembers();

        return $this->render('admin/accounts/show.html.twig', [
            'account' => $account,
            'members' => $member,
            'errors' => ''
        ]);
    }

    /**
     * @Route("/{id}/email", name="email_user_update")
     *
     */
    public function emailUpdateAction(Request $request, Customer $member)
    {
        $manager = $this->manager('customer');

        $email = $member->getEmail();

        $form = $this->createForm(EmailMemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $helper = $this->getRegisterHelper();
            $account = $member->getAccount();
            $newEmail = $member->getEmail();

            if ($member->isMasterOwner() && !$helper->emailCanBeUsed($newEmail) && $account->getEmail() == $newEmail
                || $helper->emailCanBeUsed($newEmail)) {

                $manager->save($member);

                $owner = false;
                if ($member->isMasterOwner()) {
                    $owner = true;

                    $account->setEmail($newEmail);
                    $manager->save($account);
                }

                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $user = $member->getUser();
                $user->setEmail($newEmail);
                $user->setEmailCanonical($newEmail);
                $user->setUsername($newEmail);
                $user->setUsernameCanonical($newEmail);

                $userManager->updateUser($user);

                return $this->json([
                    'id_email' => $member->getId(),
                    'email' => $member->getEmail(),
                    'owner' => $owner
                ],Response::HTTP_OK);

            }

            return $this->json([],Response::HTTP_CONFLICT);
        }

        return $this->json([
            'form_email' => $this->renderView('admin/accounts/email_form.html.twig', [
                'member' => $member,
                'errors' => $form->getErrors(true),
                'form' => $form->createView()
            ])
        ]);

    }


    /**
     * @return \AppBundle\Service\Mailer
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
    }

    /**
     * Process:
     * Account Logo and
     * Owner Profile Picture and
     * Persist Entities: Account, Member
     *
     * @param BusinessInterface $entity
     */
    private function processAndPersist(BusinessInterface $entity)
    {
        $context = $entity->isMember() ? 'profile' : 'company';

        if (null != $filename = $this->getSessionStorage()->get($context)) {

            $currentMedia = $entity->getMedia();

            $media = $this->getUploadHelper()->createMedia($filename, $context);

            if ($media instanceof MediaInterface) {

                $entity->setMedia($media);

                if ($currentMedia instanceof MediaInterface) {
                    $this->getMediaManager()->delete($currentMedia);
                }
            }
        }

        $this->getSessionStorage()->remove($context);

        if($entity->isMember()){

            $user = $entity->getUser();

            // Add Role Owner
            if(!$user->hasRole(UserInterface::ROLE_OWNER)) {

                $user->addRole(UserInterface::ROLE_OWNER);

                $this->getUserManager()->updateUser($user);
            }

            $this->processAndPersist($entity->getAccount());
        }
        
        $this->manager('customer')->save($entity);
    }

    /**
     * @return \Sonata\ClassificationBundle\Model\ContextInterface
     */
    private function getAccountContext()
    {
        return $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);
    }

    /**
     * @return \Sonata\ClassificationBundle\Model\ContextInterface
     */
    private function getMemberContext()
    {
        return $this->getContextManager()->find(BusinessInterface::CONTEXT_MEMBER);
    }

    /**
     * @return \FOS\UserBundle\Util\TokenGenerator
     */
    private function getTokenGenerator()
    {
        return $this->get('fos_user.util.token_generator');
    }

    /**
     * @return \AppBundle\Service\RegisterHelper|object
     */
    private function getRegisterHelper()
    {
        return $this->get('app.register_helper');
    }

    /**
     * @param $filterStates
     * @return array
     */
    private function getStates($filterStates)
    {
        $allStates = Brazil::states();

        if ($this->member()->isPlatformExpanse()) {

            $states = [];

            foreach ($filterStates as $state) {
                $states[$state] = $allStates[$state];
            }

            asort($states);

            return $states;
        }

        return $allStates;
    }
}

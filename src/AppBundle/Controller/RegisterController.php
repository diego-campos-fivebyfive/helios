<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Util\Validator\Document;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Extra\AccountRegister;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Form\Extra\AccountRegisterType;
use AppBundle\Form\Extra\PreRegisterType;
use AppBundle\Model\Document\Account;
use Doctrine\DBAL\Schema\View;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Notifier\Sender;

/**
 * @Route("/register")
 */
class RegisterController extends AbstractController
{
    /**
     * @Route("/pre", name="app_register")
     */
    public function registerAction(Request $request)
    {
        $registerManager = $this->getAccountRegisterManager();
        $register = $registerManager->create();
        $errors = [];

        $form = $this->createForm(AccountRegisterType::class, $register);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $checkResponse = $this->preValidateRegister($register, $form);

            if ($checkResponse instanceof RedirectResponse)
                return $checkResponse;

            if ($form->isValid()) {

                $register->setConfirmationToken($this->getTokenGenerator()->generateToken());

                $registerManager->save($register);

                return $this->redirectToRoute('app_register_link', ['id' => $register->getId()]);
            }

            $errors = $form->getErrors(true);
        }

        return $this->render('register.register', [
            'form' => $form->createView(),
            'register' => $register,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/", name="pre_register")
     */
    public function preRegisterAction(Request $request)
    {

        $getErrorArgs = function ($form) {
            return [
                'message' => $form->getErrors(true),
                'view' => $form->createView()
            ];
        };

        $throwError = function ($error) {
            return $this->render('register.pre_register', [
                'errors' => $error['message'],
                'form' => $error['view']
            ]);
        };

        $findEmailAccount = function ($email, $account) {
            return $account->findOneBy([
                'context' => 'account',
                'email' => $email
            ]);
        };

        $findEmailMember = function ($email, $account) {
            return $account->findOneBy([
                'context' => 'member',
                'email' => $email
            ]);
        };

        $findEmailUser = function ($email, $user) {
            return $user->findUserByEmail($email);
        };

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $accountManager = $this->getCustomerManager();

        $form = $this->createForm(PreRegisterType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $error = $getErrorArgs($form);
            return $throwError($error);
        }

        $data = $form->getData();

        if (
            $findEmailAccount($data['email'], $accountManager) ||
            $findEmailMember($data['email'], $accountManager) ||
            $findEmailUser($data['email'], $userManager)
        ) {
            $errorMessage = new FormError('E-mail já Cadastrado');
            $form->addError($errorMessage);
            $error = $getErrorArgs($form);
            return $throwError($error);
        }

        /** @var AccountInterface $account */
        $account = $accountManager->create();
        /** @var MemberInterface $member */
        $member = $accountManager->create();

        $account->setConfirmationToken($this->getTokenGenerator()->generateToken())
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setExtraDocument($data['extraDocument'])
            ->setDocument($data['document'])
            ->setEmail($data['email'])
            ->setState($data['state'])
            ->setCity($data['city'])
            ->setDistrict($data['district'])
            ->setStreet($data['street'])
            ->setNumber($data['number'])
            ->setPostcode($data['postcode'])
            ->setLevel('platinum')
            ->setContext(BusinessInterface::CONTEXT_ACCOUNT);
        $member->setAccount($account);

        $user = $userManager->createUser();
        $user->setEmail($data['email'])
            ->setUsername($data['email'])
            ->setPlainPassword(uniqid())
            ->setCreatedAt(new \DateTime('now'))
            ->addRole(UserInterface::ROLE_OWNER_MASTER);

        $member->setConfirmationToken($this->getTokenGenerator()->generateToken())
            ->setFirstname($data['contact'])
            ->setPhone($data['phone'])
            ->setEmail($data['email'])
            ->setContext(BusinessInterface::CONTEXT_MEMBER)
            ->setUser($user);

        $accountManager->save($account);

        $this->get('app_mailer')->sendAccountConfirmationMessage($account);

        $this->get('notifier')->notify([
            'Evento' => '206',
            'Callback' => 'account_created',
            'Id' => $account->getId()
        ]);

        $request->getSession()->set('account_id', $account->getId());

        return $this->redirectToRoute('register_in_progress');
    }

    /**
     * @Route("/in-progress", name="register_in_progress")
     */
    public function inProgressAction(Request $request)
    {
        $id = $request->getSession()->get('account_id', 0);

        $account = $this->manager('account')->find($id);

        return $this->render('register.in_progress', [
            'account' => $account
        ]);
    }

    /**
     * @Route("/", name="app_register_link")
     */
    public function linkAction(AccountRegister $register)
    {
        $maxAttempts = 3;
        $manager = $this->getAccountRegisterManager();

        $linkAttempts = $register->getParameter('link_attempts', 0);
        $linkAttempts++;

        if ($maxAttempts && $linkAttempts <= $maxAttempts) {

            $register->setParameter('link_attempts', $linkAttempts);
            $manager->save($register);

            $mailer = $this->getMailer();
            //$mailer->enableSender = false;
            $mailer->sendAccountConfirmationMessage($register);
        }

        return $this->render('register.feedback', [
            'register' => $register,
            'link_attempts' => $linkAttempts,
            'max_attempts' => $maxAttempts
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="app_register_confirm")
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var AccountInterface $account */
        $account = $this->manager('account')->findOneBy(['confirmationToken' => $token]);

        if(!$account){
            throw $this->createNotFoundException('Conta não encontrada');
        }

        $message = 'Conta não identificada!';

        if ($account->isConfirmed()) {

            $owner = $account->getOwner();
            $user = $owner->getUser();

            if (!$user->getLastActivity()) {

                $user->setConfirmationToken($token);
                $this->getUserManager()->updateUser($user);

                return $this->redirectToRoute('app_user_confirm', [
                    'token' => $token
                ]);
            }

            $message = 'Esta conta já foi ativada!';
        }

        return $this->render('register.confirm_error', [
            'message' => $message
        ]);
    }

    /**
     * @Route("/confirm/{token}/user", name="app_user_confirm")
     */
    public function confirmUserAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw $this->createNotFoundException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $manager = $this->manager('account');
            /** @var AccountInterface $account */
            if(null != $account = $manager->findOneBy(['confirmationToken' => $token])) {
                $this->getRegisterHelper()->finishAccountRegister($account, false);
                $manager->save($account);
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('app_index');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Register:confirm.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true)
        ));
    }

    /**
     * @Route("/confirm/{token}/user/legacy", name="app_user_confirm_legacy")
     */
    public function legacyConfirmUserAction(Request $request, $token)
    {
        $errors = [];
        $manager = $this->getCustomerManager();

        $member = $manager->findOneBy([
            'confirmationToken' => $token
        ]);

        if ($member instanceof BusinessInterface) {

            if ($member->getUser())
                return $this->redirectToRoute('app_index');

            /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
            $formFactory = $this->get('fos_user.registration.form.factory');
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $user = $userManager->createUser();
            $user
                ->setEmail($member->getEmail())
                ->setUsername($member->getEmail());

            $form = $formFactory->createForm();
            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                $memberData = $request->request->get('member');
                if (!$memberData['firstname']) {
                    $form->addError(new FormError('Full name is required'));
                }
                if ('on' != $memberData['terms']) {
                    $form->addError(new FormError('You must accept the terms of use'));
                }

                $member
                    ->addAttribute('terms', $memberData['terms'])
                    ->setFirstname($memberData['firstname']);

                if ($form->isValid()) {

                    $member->setUser($user);

                    //$this->getRegisterHelper()->finishMemberRegistration($member);

                    $event = $this->createWoopraEvent('registrou', [
                        'email' => $member->getEmail(),
                        'name' => $member->getName(),
                        'company' => $member->getAccount()->getName(),
                        'profile' => $member->isOwner() ? 'Dono da conta' : 'Agente'
                    ]);

                    $url = $this->generateUrl('app_index', [
                        'woopra_event' => $event->getId()
                    ]);

                    $response = new RedirectResponse($url);

                    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                    return $response;
                }

                $errors = $form->getErrors(true);
            }

            return $this->render('register.register', [
                'member' => $member,
                'form' => $form->createView(),
                'errors' => $errors
            ]);
        }

        throw $this->createNotFoundException('User not found');
    }

    /**
     * Force not found exception
     */
    public function forceNotFoundException()
    {
        throw $this->createNotFoundException();
    }

    /**
     * @param AccountRegister $register
     */
    private function preValidateRegister(AccountRegister &$register, FormInterface &$form)
    {
        $helper = $this->getRegisterHelper();

        if (!$helper->emailCanBeUsed($register->getEmail())) {

            /**
             * This section addresses the scenario where the user
             * abandons the credentials form during account verification
             */
            /** @var BusinessInterface $member */
            $member = $helper->findBusinessByEmail($register->getEmail(), BusinessInterface::CONTEXT_MEMBER);
            if ($member instanceof BusinessInterface
                && $member->isMember()
                && !$member->getUser()
                && $member->getConfirmationToken()
            ) {
                return $this->redirectToRoute('app_user_confirm', [
                    'token' => $member->getConfirmationToken()
                ]);
            }

            $form->addError(new FormError('The informed email is already in use'));
        }

        $currentRegister = $helper->findRegisterByEmail($register->getEmail());

        if ($currentRegister instanceof AccountRegister
            && !$currentRegister->isDone()
        ) {

            $currentRegister
                ->setName($register->getName())
                ->setCompanyStatus($register->getCompanyStatus())
                ->setCompanySector($register->getCompanySector())
                ->setCompanyName($register->getCompanyName());

            $register = $currentRegister;
        }

        return null;
    }

    /**
     * @return \AppBundle\Service\Mailer
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
    }

    /**
     * @return \AppBundle\Entity\Extra\AccountRegisterManager
     */
    private function getAccountRegisterManager()
    {
        return $this->get('app.account_register_manager');
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
}

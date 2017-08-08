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
    public function preRegisterAction(Request $request){
        $accountManager = $this->getCustomerManager();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $form = $this->createForm(PreRegisterType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {

            return $this->render('register.pre_register', [
                'form' => $form->createView(),
                'errors' => $form->getErrors(true)
            ]);
        }

        $data = $form->getData();

        $email = $accountManager->findOneBy([
            'context' => 'account',
            'email' => $data['email']
        ]);

        if (isset($email)) {
            $form->addError(new FormError('E-mail já Cadastrado'));
            return $this->render('register.pre_register', [
                'form' => $form->createView(),
                'errors' => $form->getErrors(true)
            ]);
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
            ->setContext(BusinessInterface::CONTEXT_ACCOUNT);
        $member->setAccount($account);

        $user = $userManager->createUser();
        $user->setEmail($data['email'])
            ->setUsername($data['email'])
            ->setPlainPassword(uniqid())
            ->addRole(UserInterface::ROLE_OWNER_MASTER);

        $member->setConfirmationToken($this->getTokenGenerator()->generateToken())
            ->setFirstname($data['contact'])
            ->setPhone($data['phone'])
            ->setEmail($data['email'])
            ->setContext(BusinessInterface::CONTEXT_MEMBER)
            ->setUser($user);

        $accountManager->save($account);

        $this->get('notifier')->notify([
            'body' => [
                'callback' => 'account_created',
                'evento' => '206',
                'id' => $account->getId()]
        ]);

        $this->setNotice('Cadastro realizado com sucesso, verifique seu e-mail!');

        return $this->redirectToRoute('app_register_link');
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
        $register = $this->getAccountRegisterManager()->findRegisterByConfirmationToken($token);

        if ($register instanceof AccountRegister) {

            $register->setStage(AccountRegister::STAGE_INFO);

            $errors = [];
            $form = $this->createForm(AccountRegisterType::class, $register);
            $cloneRegister = clone($register);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                if ($form->isValid()) {

                    $register->setEmail($cloneRegister->getEmail());

                    $member = $this->getRegisterHelper()->finishAccountRegister($register);

                    return $this->redirectToRoute('app_user_confirm', [
                        'token' => $member->getConfirmationToken()
                    ]);
                }

                $errors = $form->getErrors(true);
            }

            return $this->render('register.register', [
                'form' => $form->createView(),
                'register' => $register,
                'errors' => $errors
            ]);
        }

        $account = $this->getCustomerManager()->findOneBy([
            'confirmationToken' => $token,
            'context' => BusinessInterface::CONTEXT_ACCOUNT
        ]);

        if ($account instanceof BusinessInterface && !$account->getOwner()) {

            $member = $account->getMembers()->first();
            return $this->redirectToRoute('app_user_confirm', [
                'token' => $member->getConfirmationToken()
            ]);
        }

        $message = sprintf('Account not found. Reference: %s', base64_decode($request->query->get('reference')));

        throw $this->createNotFoundException($message);
    }

    /**
     * @Route("/confirm/{token}/user", name="app_user_confirm")
     */
    public function confirmUserAction(Request $request, $token)
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

                    $this->getRegisterHelper()->finishMemberRegistration($member);

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
     * @return \AppBundle\Service\RegisterHelper
     */
    private function getRegisterHelper()
    {
        return $this->get('app.register_helper');
    }
}

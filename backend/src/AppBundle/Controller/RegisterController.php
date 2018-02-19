<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Util\Validator\Document;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\UserInterface;
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

/**
 * @Route("/register")
 */
class RegisterController extends AbstractController
{
    /**
     * @Route("/", name="pre_register")
     */
    public function preRegisterAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $accountManager = $this->manager('customer');

        $form = $this->createForm(PreRegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $helper = $this->getRegisterHelper();
            $data = $form->getData();

            $document = $accountManager->findBy([
                'document' => $data['document']
            ]);

            $existsDoc = false;

            if($document) {
                $form->addError(new FormError('CNPJ já Cadastrado'));
                $existsDoc = true;
            }

            if ($helper->emailCanBeUsed($data['email']) && !$existsDoc) {

                $data['confirmationToken'] = $this->getTokenGenerator()->generateToken();

                /** @var AccountInterface $account */
                $account = $accountManager->create();
                /** @var MemberInterface $member */
                $member = $accountManager->create();

                $helper->fillAccount($account, $data);

                $user = $userManager->createUser();

                $user->setEmail($data['email'])
                    ->setUsername($data['email'])
                    ->setPlainPassword(uniqid())
                    ->setRoles([
                        UserInterface::ROLE_OWNER,
                        UserInterface::ROLE_OWNER_MASTER
                    ]);

                $data['user'] = $user;
                $data['account'] = $account;

                $helper->fillMember($member, $data);

                $accountManager->save($account);

                $this->getMailer()->sendAccountVerifyMessage($account);

                $request->getSession()->set('account_id', $account->getId());

                return $this->redirectToRoute('register_in_progress');
            }

            $form->addError(new FormError('E-mail já Cadastrado'));
        }

        return $this->render('register.pre_register', [
            'errors' => $form->getErrors(true),
            'form' => $form->createView()
        ]);
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

        if ($account->isApproved()) {

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
     * @Route("/verify/{token}", name="app_register_verify")
     */
    public function verifyAction(Request $request, $token)
    {
        /** @var AccountInterface $account */
        $account = $this->manager('account')->findOneBy(['confirmationToken' => $token]);

        $message = 'Operação inválida';

        if($account) {
            if($account->isPending()) {
                $member = $account->getOwner();

                $member->setStatus(AccountInterface::STANDING);
                $account->setStatus(AccountInterface::STANDING);

                $newToken = self::getTokenGenerator()->generateToken();
                $account->setConfirmationToken($newToken);

                $this->manager('account')->save($account);
            }

            if ($account->isStanding()) {
                return $this->redirectToRoute('app_account_verify', ['id' => $account->getId()]);
            }
        } else {
            $message = 'Conta não identificada';
        }

        return $this->render('register.confirm_error', [
            'message' => $message
        ]);
    }

    /**
     * @Route("/{id}/verify", name="app_account_verify")
     */
    public function accountVerifyAction(Customer $account)
    {
        return $this->render('FOSUserBundle:Register:verify.html.twig', [
            'account' => $account
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

                $member = $account->getOwner();

                $member->setStatus(AccountInterface::ACTIVATED);
                $account->setStatus(AccountInterface::ACTIVATED);

                $manager->save($account);
            }

            $member = $user->getInfo();

            if ($member->getStatus() != 3) {
                $member->setStatus(AccountInterface::ACTIVATED);

                $this->manager('member')->save($member);
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
        $manager = $this->manager('customer');

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
     * @return \AppBundle\Service\Mailer
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
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

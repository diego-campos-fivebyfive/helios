<?php

namespace AppBundle\Controller;

use AppBundle\Configuration\World;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use AppBundle\Form\Extra\AccountEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\MemberType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("member")
 * @Security("has_role('ROLE_USER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Users", route={"name"="member_index"})
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/", name="member_index")
     * @Security("has_role('ROLE_OWNER')")
     */
    public function indexAction(Request $request)
    {
        $account = $this->getCurrentAccount();

        $activeMembers = $account->getActiveMembers();
        $inactiveMembers = $account->getInactiveMembers();
        $invitedMembers = $account->getInvitedMembers();

        return $this->render('member.index', array(
            'activeMembers' => $activeMembers,
            'inactiveMembers' => $inactiveMembers,
            'invitedMembers' => $invitedMembers,
            'account' => $account
        ));
    }

    /**
     * @Route("/create", name="member_create")
     * @Method({"get", "post"})
     * @Security("has_role('ROLE_OWNER')")
     * @Breadcrumb("Add User")
     */
    public function createAction(Request $request)
    {
        $account = $this->getCurrentAccount();

        if ($account->getMembers()->count() >= $account->getMaxMembers()) {
            return $this->render('locked_content', [
                'title' => 'account.max_members.title',
                'message' => 'account.max_members.message',
                //'include' => 'member.locked_links'
            ]);
        }

        $manager = $this->getCustomerManager();
        //$context = $this->getContextManager()->find();

        $member = $manager->create();
        $member
            ->setContext(BusinessInterface::CONTEXT_MEMBER)
            ->setAccount($account);

        $form = $this->createForm(MemberType::class, $member, [
            'current_member' => $this->getCurrentMember()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \AppBundle\Service\RegisterHelper $helper */
            $helper = $this->get('app.register_helper');

            if ($helper->emailCanBeUsed($member->getEmail())) {

                $member->setConfirmationToken($this->getTokenGenerator()->generateToken());
                $manager->save($member);

                /** @var \AppBundle\Service\Mailer $mailer */
                $mailer = $this->get('app_mailer');
                //$mailer->enableSender = false;
                $mailer->sendMemberConfirmationMessage($member);

                //$this->setNotice('Usuário cadastrado com sucesso! Aguardando confirmação via email.');

                return $this->jsonResponse([
                    'message' => $this->translate('Invitation sent successfully')
                ], Response::HTTP_CREATED);
                //return $this->redirectToRoute('member_index');
            }

            $form->addError(new FormError('The informed email is already in use'));
        }

        return $this->render('member.form_content', [
            'form' => $form->createView(),
            'member' => $member,
            'errors' => $form->getErrors(true)
        ]);
    }

    /**
     * @Breadcrumb("Edit")
     * @Route("/{token}/update", name="member_update")
     */
    public function updateAction(Request $request, Customer $member)
    {
        $this->checkAccess($member);
        
        $form = $this->createForm(MemberType::class, $member, [
            'current_member' => $this->getCurrentMember()
        ]);

        if(!$member->isOwner() && !$this->getUser()->isOwner()){
            $form->remove('isOwner')->remove('team');
        }

        $form->handleRequest($request);

        $errors = $this->getValidator()->validate($member);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->processAndPersist($member);

            //$this->setNotice('Perfil atualizado com sucesso!');
            return $this->jsonResponse([
                'message' => $this->translate('User updated successfully')
            ], Response::HTTP_CREATED);

            //return $this->redirectToRoute('member_index');
        }

        return $this->render('member.form_content', [
            'form' => $form->createView(),
            'member' => $member,
            'errors' => $errors
        ]);
    }

    /**
     * @Security("has_role('ROLE_OWNER')")
     * @Route("/pre-register", name="member_register_checker")
     * @Method("post")
     */
    public function registerCheckerAction(Request $request)
    {
        $context = $this->getContextManager()->find(BusinessInterface::CONTEXT_MEMBER);

        $manager = $this->getCustomerManager();

        /** @var BusinessInterface $member */
        $member = $manager->create();
        $member->setContext($context);

        $form = $this->createForm(MemberType::class, $member);

        $form->handleRequest($request);

        $preUser = $member->getUser();

        $userManager = $this->getUserManager();

        $credentials = ['email' => true,  'username' => true];

        if(null != $email = $preUser->getEmail()){
            if(null != $user = $userManager->findUserByUsernameOrEmail($email)){
                $credentials['email'] = false;
            }
        }

        if(null != $username = $preUser->getUsername()){
            if(null != $user = $userManager->findUserByUsernameOrEmail($username)){
                $credentials['username'] = false;
            }
        }

        if(!$credentials['email'] || !$credentials['username']){
            return $this->jsonResponse([], Response::HTTP_IM_USED);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/{token}/delete", name="member_delete")
     * @Method("delete")
     */
    public function deleteAction(Customer $member)
    {
        if($member->getId() != $this->getCurrentMember()->getId()) {
            if (!$member->isDeleted()) {
                $this->getCustomerManager()->delete($member);
            }
        }

        return $this->jsonResponse(['status' => 'deleted']);
    }

    /**
     * @Route("/{token}/restore", name="member_restore")
     * @Method("post")
     */
    public function restoreAction(Customer $member)
    {
        $account = $this->getCurrentAccount();

        if ($account->getMembers()->count() >= $account->getMaxMembers()) {
            return $this->jsonResponse([
                'status' => 'error',
                'error' => $this->translate('Limit of users reached')
            ]);
        }

        if($member->getId() != $this->getCurrentMember()->getId()) {
            if($member->isDeleted()){
                $this->getCustomerManager()->restore($member);
            }
        }

        return $this->jsonResponse(['status' => 'restored']);
    }


    /**
     * @Route("/profile", name="member_profile")
     * @Breadcrumb("My data")
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }

        $member = $user->getInfo();

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        if($request->isMethod('post')){

            $userData = $request->request->get($form->getName());

            if($userData['current_password']){

                $form->handleRequest($request);

                if($form->isValid()) {
                    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                    $userManager = $this->get('fos_user.user_manager');
                    $userManager->updateUser($user);
                }
            }

            $member->setFirstname($request->request->get('firstname'));
            $memberErrors = $this->getValidator()->validate($member, 'profile');

            if ($memberErrors->count()) {
                $form->addError(new FormError($memberErrors->get(0)->getMessage(), null, [], null, 'firstname'));
            }else{
                $this->getCustomerManager()->save($member);
            }
        }

        $errors = $form->getErrors(true);

        if(!$errors->count() && $request->isMethod('post')){
            $this->setNotice('Dados atualizados com sucesso!');

            $event = $this->createWoopraEvent('property update');

            return $this->redirectToRoute('member_profile', [
                'woopra_event' => $event->getId()
            ]);
        }

        return $this->render('member.profile', [
            'form' => $form->createView(),
            'member' => $member,
            'errors' => $errors,
            'woopraEvent' => $this->requestWoopraEvent($request)
        ]);
    }

    /**
     * @Route("/timezone", name="member_timezone")
     * @Breadcrumb("Fuso Horário")
     */
    public function timezoneAction(Request $request)
    {
        if($request->isMethod('post')){

            $timezone = $request->request->get('timezone');

            $member = $this->getCurrentMember();

            $member->setTimezone($timezone);

            $this->getCustomerManager()->save($member);

            return $this->jsonResponse([
                'timezone' => $timezone
            ], Response::HTTP_ACCEPTED);
        }

        if(null != $load = $request->get('load')){

            $data = World::countries();

            return $this->jsonResponse($data, Response::HTTP_OK);
        }

        //$countries = World::countries();

        return $this->render('member.timezone', [
            'timezone' => date_default_timezone_get()
        ]);
    }

    /**
     * @Breadcrumb("My business")
     * @Route("/business", name="member_business")
     * @Security("has_role('ROLE_OWNER_MASTER')")
     */
    public function businessAction(Request $request)
    {
        $account = $this->getCurrentAccount();

        $form = $this->createForm(AccountEditType::class, $account);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $name = null;
            if('has_company' == $account->getAttribute('companyStatus')){
                $name = $account->getAttribute('companyName');
            }

            $account->setFirstname($name);

            $this->getCustomerManager()->save($account);

            $this->setNotice('Dados atualizados com sucesso!');

            $event = $this->createWoopraEvent('property update');

            return $this->redirectToRoute('member_business', [
                'woopra_event' => $event->getId()
            ]);
        }

        return $this->render('member.account', [
            'form' => $form->createView(),
            'woopraEvent' => $this->requestWoopraEvent($request)
        ]);
    }

    /**
     * @param BusinessInterface $entity
     */
    private function processAndPersist(BusinessInterface $entity)
    {
        /*$context = 'profile';
        if (null != $filename = $this->getSessionStorage()->get($context))
        {
            $currentMedia = $entity->getMedia();

            $media = $this->getUploadHelper()->createMedia($filename, $context);

            if ($media instanceof MediaInterface) {

                $entity->setMedia($media);

                if ($currentMedia instanceof MediaInterface)
                {
                    $this->getMediaManager()->delete($currentMedia);
                }
            }
        }
        $this->getSessionStorage()->remove($context);*/

        if ($entity->isOwner()) {
            $entity->setTeam(null);
        }

        if(null != $user = $entity->getUser()) {
            if ($entity->getAttribute('is_owner')) {
                $user->addRole(UserInterface::ROLE_OWNER);
            }else{
                $user->removeRole(UserInterface::ROLE_OWNER);
            }
        }

        $this->getCustomerManager()->save($entity);
    }

    /**
     * @param Customer $member
     * @return bool
     */
    private function checkAccess(Customer $member)
    {
        if (!$member->getUser() || ($member->getUser()->getId() != $this->getUser()->getId())) {

            if (!$this->getUser()->getInfo()->isOwner()
                || $member->getAccount()->getId() != $this->getCurrentAccount()->getId()
            )
                throw $this->createAccessDeniedException();
        }

        return true;
    }

    /**
     * @return \FOS\UserBundle\Util\TokenGenerator
     */
    private function getTokenGenerator()
    {
        return $this->get('fos_user.util.token_generator');
    }
}

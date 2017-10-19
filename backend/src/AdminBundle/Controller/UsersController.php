<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Customer;
use AdminBundle\Form\MemberType;
use AppBundle\Entity\UserInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\AccountInterface;
use AppBundle\Controller\AbstractController;;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("users")
 *
 * @Breadcrumb("Sices")
 */
class UsersController extends AbstractController
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $manager = $this->manager('customer');

        /** @var MemberInterface $member */
        $members = $manager->findBy([
            'context' => MemberInterface::CONTEXT,
            'account' => $this->account()
        ]);

        return $this->render('admin/user/index.html.twig', array(
            'members' => $members
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/create", name="create_user")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $memberManager = $this->manager('customer');

        /** @var AccountInterface $accountManager */
        $accountManager = $this->get('account_manager');
        $account = $accountManager->findOneBy([
            'email' => 'servidor@sicesbrasil.com.br'
        ]);

        $user = $userManager->createUser();
        $user->setCreatedAt(new \DateTime('now'));

        $member = $memberManager->create();
        $member
            ->setContext(Customer::CONTEXT_MEMBER)
            ->setAccount($account)
            ->setUser($user);

        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $helper = $this->getRegisterHelper();

            if($helper->emailCanBeUsed($data->getEmail())) {
                $user->addRole(UserInterface::ROLE_PLATFORM_COMMERCIAL);

                $memberManager->save($member);

                return $this->redirectToRoute('user_index');
            }

            $form->addError(new FormError('Este email não pode ser usado'));
        }

        return $this->render('admin/user/form.html.twig', array(
            'errors' => $form->getErrors(true),
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(Customer $member)
    {
        return $this->render('admin/user/show.html.twig', array(
            '_member' => $member,
            'is_admin' => $member->isPlatformAdmin()
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/update/{id}", name="user_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request, Customer $member)
    {
        $memberManager = $this->manager('customer');

        $email = $member->getEmail();

        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $helper = $this->getRegisterHelper();

            if($email != $data->getEmail() && !$helper->emailCanBeUsed($data->getEmail())) {
                $form->addError(new FormError('Este email não pode ser usado'));
            } else {

                if($member->getUser()->isEnabled()){
                    $member->restore();

                    $member->getUser()->addRole(UserInterface::ROLE_PLATFORM_COMMERCIAL);

                    $memberManager->save($member);

                }else{

                    $memberManager->delete($member);
                }

                return $this->redirectToRoute('user_index');
            }
        }

        return $this->render('admin/user/form.html.twig', array(
            'errors' => $form->getErrors(true),
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("delete")
     */
    public function deleteAction(Customer $member)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        try {
            $this->manager('customer')->delete($member);
            $userManager->deleteUser($member->getUser());
            $status = Response::HTTP_OK;
        } catch (\Exception $exception) {
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json([], $status);

    }

    /**
     * @return \AppBundle\Service\RegisterHelper|object
     */
    private function getRegisterHelper()
    {
        return $this->get('app.register_helper');
    }
}

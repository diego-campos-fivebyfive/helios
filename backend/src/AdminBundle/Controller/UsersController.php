<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\MemberType;
use AdminBundle\Form\UserType;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * User controller.
 *
 * @Route("user")
 * @Security("has_role('ROLE_ADMIN')")
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
        /** @var Customer $manager */
        $manager = $this->manager('customer');

        /** @var MemberInterface $member */
        $members = $manager->findBy([
            'context' => 'member'
        ]);
        $data = [];
        foreach ($members as $member) {
            if (($member->isPlatformAdmin() || $member->isPlatformCommercial()) && !$member->isDeleted())
                $data[] = $member;
        }

        return $this->render('admin/user/index.html.twig', array(
            'members' => $data,
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
        $account = $accountManager->find(634);

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

            $user->addRole(UserInterface::ROLE_PLATFORM_COMMERCIAL);

            $memberManager->save($member);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/form.html.twig', array(
           'form' => $form->createView(),
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
            'member' => $member,
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

        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $member->getUser()->addRole(UserInterface::ROLE_PLATFORM_COMMERCIAL);

            $memberManager->save($member);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/form.html.twig', array(
            'form' => $form->createView(),
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
}

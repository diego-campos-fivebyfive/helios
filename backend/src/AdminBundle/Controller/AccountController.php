<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\AccountType;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\UserInterface;
use AdminBundle\Controller\AdminController;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @TODO Remove Security::has_role('ROLE_ADMIN') after production is established
 */

/**
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PLATFORM_MASTER') or has_role('ROLE_PLATFORM_ADMIN')")
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

        /** @var Customer $manager */
        $manager = $this->manager('customer');

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from(Customer::class, 'a')
            ->where('a.context = :context')
            ->setParameters([
                'context' => $this->getAccountContext()
            ]);

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('admin/accounts/index.html.twig', array(
            'pagination' => $pagination,
            'accounts' => $qb
        ));
    }

    /**
     * @Breadcrumb("New Account")
     * @Route("/create", name="account_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('customer');

        $accountContext = $this->getAccountContext();
        $memberContext = $this->getMemberContext();

        $account = $manager->create();
        $account->setContext($accountContext);

        $member = $manager->create();
        $member->setContext($memberContext);

        $account->addMember($member);

        $form = $this->createForm(AccountType::class, $account);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->processAndPersist($member);
            $this->setNotice("Conta criada com sucesso !");
            return $this->redirectToRoute('account_index', [
                'id' => $account->getId()
            ]);
        }

        return $this->render('admin/accounts/form.html.twig', array(
            'form' => $form->createView(),
            'account' => $account
        ));
    }

    /**
     * @Breadcrumb("update.account")
     * @Route("/{id}/update", name="account_update")
     */
    public function updateAction(Request $request, Customer $account)
    {
        $manager = $this->manager('customer');

        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->save($account);

            $this->setNotice("Conta atualizada com sucesso !");
            return $this->redirectToRoute('account_index');

        }

        return $this->render('admin/accounts/form.html.twig', [
            'errors' => $form->getErrors(true),
            'form' => $form->createView(),
            'account' => $account
        ]);
    }



    /**
     * @Route("/{token}/change", name="account_change_status")
     * //@Method("post")
     */
    public function changeAction(Customer $account)
    {
        try {
            if($account->isVerified() || $account->isConfirmed()) {
                $account = $this->changeStatus($account, BusinessInterface::CONFIRMED);

                $this->getMailer()->sendAccountConfirmationMessage($account);
            } elseif ($account->isActivated()) {
                foreach ($account->getMembers() as $member){
                    $member->getUser()->setEnabled(0);
                }

                $this->changeStatus($account, BusinessInterface::LOCKED);
            } elseif ($account->isLocked()) {
                foreach ($account->getMembers() as $member){
                    $member->getUser()->setEnabled(1);
                }

                $this->changeStatus($account, BusinessInterface::ACTIVATED);
            }

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
            'members' => $member
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
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\UserInterface;
use AppBundle\Form\AccountType;
use AppBundle\Form\CustomerType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * AccountController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Accounts", route={"name"="account_index"})
 *
 * @Route("account")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account_index")
     */
    public function indexAction()
    {
        $manager = $this->manager('customer');

        $accounts = $manager->findBy([
            'context' => $this->getAccountContext()
        ]);

        return $this->render('AppBundle:Account:index.html.twig', array(
            'accounts' => $accounts
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
            return $this->redirectToRoute('account_show', [
                'token' => $account->getToken()
            ]);
        }

        return $this->render('AppBundle:Account:form.html.twig', array(
            'form' => $form->createView(),
            'account' => $account
        ));
    }

    /**
     * @Breadcrumb("update.account")
     * @Route("/{token}/update", name="account_update")
     */
    public function updateAction(Request $request, Customer $account)
    {
        $member = $this->getCurrentMember();
        $manager = $this->manager('customer');

        /**
         * Prevent excess records in the form of listing,
         * including only the first owner of the account
         */
        //$account->edition = true;

        $form = $this->createForm(AccountType::class, $account);

        if($member->getAccount()->getId() == $account->getId())
            $form->remove('status');

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->save($account);

            $this->processAndPersist($account->getMembers()->first());
            $this->setNotice("Conta atualizada com sucesso !");
            return $this->redirectToRoute('account_index');
        }

        return $this->render('AppBundle:Account:form.html.twig', array(
            'form' => $form->createView(),
            'account' => $account
        ));
    }

    /**
     * @Route("/{token}", name="account_show")
     */
    public function showAction(Request $request, Customer $account)
    {
        return $this->render('account.show', [
            'account' => $account
        ]);
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

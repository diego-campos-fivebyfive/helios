<?php

namespace AppBundle\Controller;

use AdminBundle\Form\Misc\RankingType;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Misc\Ranking;
use AppBundle\Service\Business\RankingGenerator;
use AppBundle\Service\Stock\Identity;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/ranking")
 * @Breadcrumb("Pontuações")
 */
class RankingController extends AbstractController
{
    /**
     * @var array
     */
    private $likes = ['a.firstname', 'a.lastname', 'a.document', 'a.email'];

    /**
     * @Route("/", name="ranking_index")
     */
    public function indexAction()
    {
        return $this->render('ranking/index.html.twig');
    }

    /**
     * @Route("/list_accounts", name="list_accounts")
     */
    public function accountsListAction(Request $request)
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

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('ranking/account.html.twig', array(
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/{id}/transaction", name="transaction_ranking")
     */
    public function transactionAction(Customer $account)
    {
        $amount = $this->currentAmount($account);

        return $this->render('ranking/transaction.html.twig',[
            'account' => $account,
            'amount' => $amount
        ]);
    }

    /**
     * @Route("/transactions/{id}/list", name="list_transactions_ranking")
     */
    public function listTransactionAction(Request $request, Customer $account)
    {
        $target = Identity::create($account);

        $manager = $this->manager('ranking');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from(Ranking::class, 'r')
            ->where('r.target = :target')
            ->orderBy('r.createdAt', 'desc')
            ->setParameter('target', $target);

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('ranking/transaction_content.html.twig',[
            'pagination' => $pagination,
            'account' => $account
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/transactions/{id}/create", name="create_ranking")
     */
    public function transactionsAction(Request $request, Customer $account)
    {
        $form = $this->createForm(RankingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $rankingGenerator = $this->get('ranking_generator');

            $rankingGenerator->create($account, $data['description'], $data['amount']);

            return $this->json([]);
        }

        return $this->render('ranking/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("transactions/{id}/edit", name="transaction_edit")
     */
    public function transactionEditAction(Request $request, Ranking $ranking)
    {
        $form = $this->createForm(RankingType::class, $ranking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager('ranking')->save($ranking);

            return $this->json([]);
        }

        return $this->render('ranking/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/transaction/{id}/delete", name="delete_ranking")
     *
     * @Method("delete")
     */
    public function transactionDeleteAction(Ranking $ranking)
    {
        $manager = $this->manager('ranking');

        $manager->delete($ranking);

        return $this->json([]);
    }

    private function currentAmount($account)
    {
        $rankingGenerator = $this->get('ranking_generator');

        $transactions = $rankingGenerator->load($account);

        $amount = null;
        foreach ($transactions as $transaction) {
            $amount += $transaction->getAmount();
        }

        return $amount;
    }

    /**
     * @return \Sonata\ClassificationBundle\Model\ContextInterface
     */
    private function getAccountContext()
    {
        return $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);
    }
}

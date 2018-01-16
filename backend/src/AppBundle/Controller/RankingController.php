<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @Security("has_role('ROLE_OWNER') or has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
 *
 * @Route("/ranking")
 * @Breadcrumb("Ranking")
 */
class RankingController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        //$families = Maker::getContextList();

        return $this->render('ranking/index.html.twig', [
          //  'families' => $families
        ]);
    }

    /**
     * @Route("/accounts", name="accounts_ranking")
     */
    public function accountsAction(Request $request)
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

//        if(-1 != $state = $request->get('state', -1)){
//            $qb->andWhere('a.state = :state');
//            $qb->setParameter('state', $state);
//        }
//
//        if(-1 != $bond = $request->get('bond', -1)){
//            $qb->andWhere('a.agent = :bond');
//            $qb->setParameter('bond', $bond);
//        }
//
//        if(-1 != $status = $request->get('status', -1)){
//            $qb->andWhere('a.status = :status');
//            $qb->setParameter('status', $status);
//        }
//
//        if(-1 != $level = $request->get('level', -1)) {
//            $qb->andWhere('a.level = :level');
//            $qb->setParameter('level', $level);
//        }
//
//        $expanseStates = [];
//        if ($this->member()->isPlatformExpanse()) {
//
//            $expanseStates = $this->member()->getAttributes()['states'];
//
//            $qb->andWhere($qb->expr()->in('a.state', $expanseStates));
//        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

//        /** @var MemberInterface $member */
//        $members = $manager->findBy([
//            'context' => MemberInterface::CONTEXT,
//            'account' => $this->account()
//        ]);
//
//        $membersSices = [];
//        foreach ($members as $i => $member) {
//            if($member->getUser()->isEnabled() && $member->isPlatformCommercial()) {
//                $membersSices[$i] = $member;
//            }
//        }

//        $levels = Memorial::getDefaultLevels();
//
//        unset($levels[Memorial::LEVEL_PROMOTIONAL], $levels[Memorial::LEVEL_FINAME]);
//
//        $states = $this->getStates($expanseStates);

        return $this->render('ranking/accounts_content.html.twig', array(
//            'current_state' => $state,
//            'current_status' => $status,
//            'allStatus' =>Customer::getStatusList(),
//            'current_level' => $level,
//            'allLevels' => $levels,
//            'current_bond' => $bond,
//            'members' => $membersSices,
//            'states' => $states,
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/{id}/transaction", name="transaction_ranking")
     */
    public function transactionAction(Customer $account)
    {
        return $this->render('ranking/transaction.html.twig',[
            'account' => $account
        ]);
    }

    /**
     * @Route("/transaction/{id}/list", name="list_transactions_ranking")
     */
    public function listTransactionAction(Request $request, Customer $account)
    {
        return $this->render('ranking/transaction_content.html.twig',[

        ]);

//        $date = $request->query->get('date',null);
//
//        $startAt = null;
//        $endAt = null;
//
//        $formatDate = function($date){
//            return implode('-', array_reverse(explode('/', $date)));
//        };
//
//        if ($date) {
//            $date = explode(' - ',$date);
//            $startAt = new \DateTime($formatDate($date[0]));
//            $endAt = new \DateTime($formatDate($date[1]));
//        }
//
//        $component = $this->manager($family)->find($id);
//
//        $products = $this->get('stock_converter')->transform([$component]);
//
//        $this->overrideGetFilters();
//
//        $stockQuery = $this->get('stock_query');
//
//        if ($startAt and $endAt)
//            $stockQuery->between($startAt, $endAt);
//
//        $product = $stockQuery->product($products[0]);
//
//        $page = $request->query->getInt('page',1);
//
//        return $this->render('admin/stock/transaction_content.html.twig',[
//            'pagination' => $product->pagination($page)
//        ]);
    }

    /**
     * @return \Sonata\ClassificationBundle\Model\ContextInterface
     */
    private function getAccountContext()
    {
        return $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);
    }
}
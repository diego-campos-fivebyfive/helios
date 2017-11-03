<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Stock\TransactionType;
use AppBundle\Entity\Component\Structure;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("stock")
 * @Breadcrumb("Gestão de estoque")
 */
class StockController extends AbstractController
{
    /**
     * @Route("/", name="stock")
     */
    public function stockAction()
    {

        return $this->render('admin/stock/index.html.twig');
    }

    /**
     * @Route("/components", name="components_stock")
     */
    public function componentsAction(Request $request)
    {
        // TODO: codigo para teste
        $manager = $this->manager('structure');
        $paginator = $this->getPaginator();

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('s')
            ->from(Structure::class, 's')
            ->leftJoin('s.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('s.description', 'asc');

        if(!$this->user()->isAdmin() && !$this->user()->isPlatformMaster()) {
            $qb->where('s.status = :status');
            $qb->andWhere('s.available = :available');
            $qb->setParameters([
                'status' => 1,
                'available' => 1
            ]);
        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10,
            ['distinct' => false]
        );

        return $this->render('admin/stock/components_content.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{component}/transaction", name="transaction_stock")
     */
    public function transactionAction($component)
    {
        // TODO: adaptar para pegar a familia do componente
        $manager = $this->manager('structure');

        $component = $manager->find($component);

        return $this->render('admin/stock/transaction.html.twig',[
            'component' => $component
        ]);
    }

    /**
     * @Route("/transaction/{component}/list", name="list_transactions_stock")
     */
    public function listTransactionAction(Request $request, $component)
    {
        // TODO: codigo de teste
        $manager = $this->manager('structure');

        $paginator = $this->getPaginator();

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('s')
            ->from(Structure::class, 's')
            ->leftJoin('s.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('s.description', 'asc');

        if(!$this->user()->isAdmin() && !$this->user()->isPlatformMaster()) {
            $qb->where('s.status = :status');
            $qb->andWhere('s.available = :available');
            $qb->setParameters([
                'status' => 1,
                'available' => 1
            ]);
        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10,
            ['distinct' => false]
        );

        return $this->render('admin/stock/transaction_content.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/transactions/{type}/{id}", name="transactions")
     */
    public function transactionsAction(Request $request, $type, $id)
    {
        $component = $this->manager($type)->find($id);

        $form = $this->createForm(TransactionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $amount = $data['amount'] * $data['mode'];
            $description = $data['description'];
            $stockComponent = $this->get('stock_component');

            $stockComponent->add($component, $amount, $description);

            $stockComponent->transact();

            return $this->json([]);
        }

        return $this->render('admin/stock/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

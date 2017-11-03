<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Component\Maker;
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
        $families = Maker::getContextList();

        return $this->render('admin/stock/index.html.twig', [
            'families' => $families
        ]);
    }

    /**
     * @Route("/components", name="components_stock")
     */
    public function componentsAction(Request $request)
    {
        $componentsPaginator = $this->get('component_query')->fromCriteria([
            'page' => $request->query->getInt('page',1),
            'family' => $request->query->get('type',null),
            'like' => $request->query->get('filter',null)
        ]);

        return $this->render('admin/stock/components_content.html.twig',[
            'pagination' => $componentsPaginator
        ]);
    }

    /**
     * @Route("/{id}/{family}/transaction", name="transaction_stock")
     */
    public function transactionAction($id, $family)
    {
        $component = $this->manager($family)->find($id);

        return $this->render('admin/stock/transaction.html.twig',[
            'component' => $component,
            'family' => $family
        ]);
    }

    /**
     * @Route("/transaction/{id}/{family}/list", name="list_transactions_stock")
     */
    public function listTransactionAction(Request $request, $id, $family)
    {
        $date = $request->query->get('date',null);

        $startAt = null;
        $endAt = null;

        $formatDate = function($date){
            return implode('-', array_reverse(explode('/', $date)));
        };

        if ($date) {
            $date = explode(' - ',$date);
            $startAt = new \DateTime($formatDate($date[0]));
            $endAt = new \DateTime($formatDate($date[1]));
        }

        $component = $this->manager($family)->find($id);

        $products = $this->get('stock_converter')->transform([$component]);

        $this->overrideGetFilters();

        $stockQuery = $this->get('stock_query');

        if ($startAt and $endAt)
            $stockQuery->between($startAt, $endAt);

        $product = $stockQuery->product($products[0]);

        $page = $request->query->getInt('page',1);

        return $this->render('admin/stock/transaction_content.html.twig',[
            'pagination' => $product->pagination($page)
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

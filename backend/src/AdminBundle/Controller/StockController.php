<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Component\Maker;
use AdminBundle\Form\Stock\TransactionType;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\Parameter;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE')")
 *
 * @Route("twig/stock")
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

        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        $control = $parameter->get('stock_control_families');

        $families = array_map(function ($family) use ($control) {
            return in_array($family, $control);
        },$families);

        return $this->render('admin/stock/index.html.twig', [
            'families' => $families,
            'inventory' => $this->getInventory()
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

        $this->overrideGetFilters();

        /** @var \AppBundle\Service\Stock\Query $stockQuery */
        $stockQuery = $this->get('stock_query');

        if ($startAt and $endAt)
            $stockQuery->between($startAt, $endAt);

        $product = $stockQuery->product($family, $id);

        $page = $request->query->getInt('page',1);

        return $this->render('admin/stock/transaction_content.html.twig',[
            'pagination' => $product->paginate($page)
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

            /** @var \AppBundle\Service\Stock\Component $stockComponent */
            $stockComponent = $this->get('stock_component');

            $stockComponent->add($type, $component->getId(), $amount, $description);

            $stockComponent->transact();

            return $this->json([]);
        }

        return $this->render('admin/stock/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/families", name="families_stock")
     * @Method("POST")
     */
    public function familiesAction(Request $request)
    {
        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        $families = $request->request->get('stock_control_families');

        $parameter->set('stock_control_families', $families ?? []);
        $manager->save($parameter);

        return $this->json([]);
    }

    /**
     * @return array
     */
    private function getInventory()
    {
        /** @var \AppBundle\Entity\Pricing\Memorial $memorial */
        $memorial = $this->container->get('memorial_loader')->load();

        $manager = $this->manager('order_element');

        $qb = $manager->createQueryBuilder();

        $qb->select('e.code, e.family, p.status, SUM(e.quantity) as quantity')
            ->innerJoin('e.order', 'o')
            ->innerJoin('o.parent', 'p')
            ->groupBy('p.status, e.code')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('p.status', OrderInterface::STATUS_PENDING),
                $qb->expr()->eq('p.status', OrderInterface::STATUS_VALIDATED)
            ))
            ->andWhere('p.createdAt >= :publishedAt')
            ->setParameter('publishedAt', $memorial->getPublishedAt()->format('Y-m-d H:i'))
        ;

        $data = [];

        foreach ($qb->getQuery()->getResult() as $item) {
            $data[$item['status']][$item['code']] =  $item['quantity'];
        }

        return $data;
    }
}

<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Order\Order;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("ord")
 * @Breadcrumb("Orçamentos")
 */
class OrderController extends AbstractController
{

    /**
     * @Route("/", name="index_order")
     */
    public function orderAction(Request $request)
    {
        $manager = $this->manager('order');

        $qb = $manager->createQueryBuilder();
        $qb2 = $manager->getEntityManager()->createQueryBuilder();
        $qb->where(
            $qb->expr()->in('o.id',
                $qb2->select('o2')
                    ->from(Order::class, 'o2')
                    ->where('o2.parent is null')
                    ->andWhere('o2.sendAt is not null')
                    ->getQuery()->getDQL()
            )
        )->andWhere('o.account = :account')
            ->setParameter('account', $this->account());

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Order.index', array(
            'orders' => $pagination
        ));
    }
}

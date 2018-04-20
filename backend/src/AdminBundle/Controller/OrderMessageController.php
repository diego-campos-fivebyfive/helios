<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Manager\OrderMessageManager;
use AppBundle\Service\Order\OrderFinder;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Security("user.isPlatform()")
 *
 * @Route("/api/v1/orders/messages")
 */
class OrderMessageController extends AbstractController
{
    /**
     * @Route("/", name="list_order_messages_to")
     */
    public function listMessagesAction(Request $request)
    {
        /** @var OrderFinder $orderFinder */
        $orderFinder = $this->get('order_finder');

        $orderFinder
            ->set('agent', $this->member());

        $qb = $orderFinder->queryBuilder();

        $qb->select('DISTINCT(o.id)');

        $ids = array_map('current', ($qb->getQuery()->getResult()));
        $ids = $ids ? $ids : [0];

        /** @var OrderMessageManager $qb */
        $orderMessageManager = $this->get('order_message_manager');

        /** @var QueryBuilder $qb2 */
        $qb2 = $orderMessageManager->createQueryBuilder();

        $qb2->andWhere($qb2->expr()->in('m.to', $this->member()->getId()));
        $qb2->andWhere($qb2->expr()->eq('m.restricted', 1));
        $qb2->orWhere($qb2->expr()->in('m.order', $ids));

        //dump($qb2->getQuery()->getResult());die;

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb2->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @param $messageCollection
     * @return array
     */
    private function formatEntity($messageCollection)
    {
        return array_map(function($orderMesage) {
            $author = $orderMesage->getAuthor();

            if ($author) {
                $author = [
                    'id' => $author->getId(),
                    'name' => $author->getFirstName()
                ];
            } else {
                $author = [
                    'id' => '',
                    'name' => ''
                ];
            }

            return [
                'id' => $orderMesage->getId(),
                'author' => $author,
                'content' => $orderMesage->getContent()
            ];
        }, $messageCollection);
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/orders/messages/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/orders/messages/?page={$pagination[$position]}" : false;
        }

        return "/orders/messages/?page={$pagination[$position]}";
    }

    /**
     * @param $collection
     * @return array
     */
    private function formatCollection($collection)
    {
        $pagination = $collection->getPaginationData();

        return [
            'page' => [
                'total' => $pagination['pageCount'],
                'current'=> $pagination['current'],
                'links' => [
                    'prev' => $this->getPaginationLinks($pagination, 'previous'),
                    'self' => $this->getPaginationLinks($pagination, 'current'),
                    'next' => $this->getPaginationLinks($pagination, 'next')
                ]
            ],
            'size' => $pagination['totalCount'],
            'limit' => $pagination['numItemsPerPage'],
            'results' => $this->formatEntity($collection->getItems())
        ];
    }
}

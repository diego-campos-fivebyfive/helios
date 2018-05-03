<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Order\Message;
use AppBundle\Entity\User;
use AppBundle\Manager\OrderMessageManager;
use AppBundle\Service\Order\OrderFinder;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("user.isPlatform()")
 *
 * @Route("/api/v1/orders/messages")
 */
class OrderMessageController extends AbstractController
{
    /**
     * @Route("/", name="list_order_messages_to")
     * @Method("get")
     */
    public function getMessagesAction(Request $request)
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

        $qb2->andWhere($qb2->expr()->like('m.to', ':memberId'));
        $qb2->orWhere($qb2->expr()->in('m.order', $ids));
        $qb2->innerJoin('m.order', 'o');
        $qb2->innerJoin('m.author', 'c');

        $this->filterMessages($request, $qb2);

        $qb2->andWhere($qb2->expr()->eq('m.restricted', true));
        $qb2->setParameter('memberId', '%"' . $this->member()->getId() . '"%');
        $qb2->orderBy('m.createdAt', 'DESC');

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb2->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/mentions", name="list_mentions")
     * @Method("get")
     */
    public function getMentionRolesAndUsersAction()
    {
        $rolesData = [];

        $roles = User::getRolesOptions();

        foreach ($roles as $const => $role) {
            $rolesData[$const] = [
                'id' => $const,
                'name' => $role
            ];
        }

        $membersData = [];

        $members = $this->account()->getMembers();

        foreach ($members as $member) {
            $id = $member->getId();

            $membersData[$id] = [
                'id' => $id,
                'name' => $member->getFirstName()
            ];
        }

        $data = [
            'roles' => $rolesData,
            'users' => $membersData
        ];

        return $this->json($data);
    }

    /**
     * @Route("/unread_count", name="unread_message_count")
     * @Method("get")
     */
    public function getUnreadMessagesCountAction()
    {
        /** @var OrderMessageManager $qb */
        $orderMessageManager = $this->get('order_message_manager');

        /** @var QueryBuilder $qb */
        $qb = $orderMessageManager->createQueryBuilder();

        $qb->select('COUNT(m.id) AS unreadMessages');
        $qb->andWhere($qb->expr()->like('m.read', ':memberId'));
        $qb->andWhere($qb->expr()->eq('m.restricted', true));
        $qb->setParameter('memberId', '%"' . $this->member()->getId() . '"%');

        $data = $qb->getQuery()->getSingleResult();

        $data['unreadMessages'] = (int) $data['unreadMessages'];

        return $this->json($data);
    }

    /**
     * @Route("/mark_as_read", name="mark_as_read")
     * @Method("post")
     */
    public function postMarkAsReadAction(Request $request)
    {
        $messagesIds = $request->get('messagesIds');

        /** @var OrderMessageManager $messageManager */
        $messageManager = $this->get('order_message_manager');

        foreach ($messagesIds as $messageId) {
            /** @var Message $message */
            $userId = (string) "\"" . $this->member()->getId() . "\"";
            $message = $messageManager->find($messageId);

            if ($message) {
                $read = $message->getRead();

                $search = array_search($userId, $read);

                if (is_int($search) || is_string($search)) {
                    unset($read[$search]);
                }

                $message->setRead($read);
                $messageManager->save($message);
            }
        }

        return $this->json([]);
    }

    /**
     * @param Request $request
     * @param QueryBuilder $qb
     */
    private function filterMessages(Request $request, QueryBuilder &$qb)
    {
        $searchTerm = $request->get('searchTerm');
        $unreadMessages = $request->query->getBoolean('unreadMessages');

        if (!is_null($searchTerm)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('m.content', ':searchTerm'),
                    $qb->expr()->like('o.reference', ':searchTerm'),
                    $qb->expr()->like('c.firstname', ':searchTerm')
                )
            );
            $qb->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($unreadMessages) {
            $qb->andWhere($qb->expr()->like('m.read', ':memberId'));
            $qb->setParameter('memberId', '%"' . $this->member()->getId() . '"%');
        }
    }

    /**
     * @param $messageCollection
     * @return array
     */
    private function formatEntity($messageCollection)
    {
        return array_map(function($orderMessage) {
            $author = $orderMessage->getAuthor();
            $order = $orderMessage->getOrder();

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
            $order = [
                'id' => $order->getId(),
                'reference' => (string) $order->getReference()
            ];

            $memberId = "\"" . $this->member()->getId() . "\"";
            $read = $orderMessage->getRead();
            $isRead = false;

            if (!in_array($memberId, $read)) {
                $isRead = true;
            }

            /** @var \DateTime $createDate */
            $createDate = $orderMessage->getCreatedAt()->format('Y-m-d H:i:s ');

            return [
                'id' => $orderMessage->getId(),
                'author' => $author,
                'order' => $order,
                'content' => $this->decodeHtml($orderMessage->getContent()),
                'isRead' => $isRead,
                'createdAt' => $createDate
            ];
        }, $messageCollection);
    }

    /**
     * @param $contentHtml
     * @return string
     */
    private function decodeHtml($contentHtml)
    {
        $decodedHtml = html_entity_decode($contentHtml);
        $decodedHtml = strip_tags($decodedHtml);

        return $decodedHtml;
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

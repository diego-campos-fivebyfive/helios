<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Manager\Pricing\MemorialManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("/api/v1/memorials")
 */
class MemorialsController extends AbstractController
{
    /**
     * @Route("/", name="list_memorials")
     * @Method("get")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemorialsAction(Request $request)
    {
        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('memorial_manager');

        $qb = $memorialManager->createQueryBuilder();

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data);
    }

    /**
     * @Route("/{id}", name="memorial_single")
     * @Method("get")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSingleMemorialAction(Memorial $memorial)
    {
        $data = $this->formatMemorial($memorial);

        return $this->json($data);
    }

    /**
     * @Route("/", name="create_memorial")
     * @Method("post")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postMemorialAction(Request $request)
    {
        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('memorial_manager');

        $data = json_decode($request->getContent(), true);

        $memorial = $memorialManager->create();

        $memorial->setName($data['name']);

        $memorialManager->save($memorial);

        return $this->json();
    }

    /**
     * @Route("/{id}", name="update_memorial")
     * @Method("put")
     * @param Request $request
     * @param Memorial $memorial
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putMemorialAction(Request $request, Memorial $memorial)
    {
        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('memorial_manager');

        $data = json_decode($request->getContent(), true);

        $memorial->setName($data['name']);
        $memorial->setPublishedAt($data['publishedAt']);
        $memorial->setExpiredAt($data['expiredAt']);

        $memorialManager->save($memorial);

        return $this->json();
    }

    /**
     * @Route("/{id}", name="delete_memorial")
     * @Method("delete")
     * @param Memorial $memorial
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteMemorialAction(Memorial $memorial)
    {
        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('memorial_manager');

        $memorialManager->delete($memorial);

        return $this->json();
    }

    /**
     * @param $memorialCollection
     * @return array
     */
    private function formatEntity($memorialCollection)
    {
        return array_map(function(Memorial $memorial) {
            return $this->formatMemorial($memorial);
        }, $memorialCollection);
    }

    /**
     * @param Memorial $memorial
     * @return array
     */
    private function formatMemorial(Memorial $memorial)
    {
        /** @var \DateTime $createdAt */
        $publishedAt = $memorial->getPublishedAt()? $memorial->getPublishedAt()->format('Y-m-d H:i:s') : null;
        $expiredAt = $memorial->getExpiredAt() ? $memorial->getExpiredAt()->format('Y-m-d H:i:s') : null;

        return [
            'id' => $memorial->getId(),
            'name' => $memorial->getName(),
            'published_at' => $publishedAt,
            'expired_at' => $expiredAt
        ];
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/memorials/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/memorials/?page={$pagination[$position]}" : false;
        }

        return "/memorials/?page={$pagination[$position]}";
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
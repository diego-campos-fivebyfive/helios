<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Term;
use AppBundle\Manager\TermManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("user.isPlatform()")
 *
 * @Route("/api/v1/terms")
 */
class TermController extends AbstractController
{
    /**
     * @Route("/", name="list_terms")
     * @Method("get")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTermsAction(Request $request)
    {
        /** @var TermManager $termManager */
        $termManager = $this->get('term_manager');

        $qb = $termManager->createQueryBuilder();

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="create_term")
     * @Method("post")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postTermAction(Request $request)
    {
        /** @var TermManager $termManager */
        $termManager = $this->get('term_manager');

        $data = json_decode($request->getContent(), true);

        /** @var Term $term */
        $term = $termManager->create();

        $term->setTitle($data['title']);
        $term->setUrl($data['url']);
        $term->setPublishedAt(new \DateTime($data['publishedAt']));

        $termManager->save($term);

        return $this->json();
    }

    /**
     * @Route("/{id}", name="update_term")
     * @Method("put")
     * @param Request $request
     * @param Term $term
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putTermAction(Request $request, Term $term)
    {
        /** @var TermManager $termManager */
        $termManager = $this->get('term_manager');

        $data = json_decode($request->getContent(), true);

        $term->setTitle($data['title']);
        $term->setUrl($data['url']);
        $term->setPublishedAt(new \DateTime($data['publishedAt']));

        $termManager->save($term);

        return $this->json();
    }

    /**
     * @Route("/{id}", name="delete_term")
     * @Method("delete")
     * @param Term $term
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteTermAction(Term $term)
    {
        /** @var TermManager $termManager */
        $termManager = $this->get('term_manager');

        $termManager->delete($term);

        return $this->json();
    }

    /**
     * @param $termCollection
     * @return array
     */
    private function formatEntity($termCollection)
    {
        return array_map(function(Term $term) {
            /** @var \DateTime $createdAt */
            $createdAt = $term->getCreatedAt()->format('Y-m-d H:i:s ');
            $updatedAt = $term->getUpdatedAt()->format('Y-m-d H:i:s ');
            $publishedAt = $term->getPublishedAt()->format('Y-m-d H:i:s ');

            return [
                'id' => $term->getId(),
                'title' => $term->getTitle(),
                'url' => $term->getUrl(),
                'publishedAt' => $publishedAt,
                'updatedAt' => $updatedAt,
                'createdAt' => $createdAt
            ];
        }, $termCollection);
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/terms/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/terms/?page={$pagination[$position]}" : false;
        }

        return "/terms/?page={$pagination[$position]}";
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

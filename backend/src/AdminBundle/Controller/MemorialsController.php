<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\MemorialManager;
use AppBundle\Service\Precifier\MemorialCloner;
use AppBundle\Service\Precifier\MemorialHelper;
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
     * @Route("/power_ranges", name="memorial_power_ranges")
     * @Method("get")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemorialPowerRanges()
    {
        $powerRanges = Range::$powerRanges;

        $result = [];

        for ($i = 0; $i < count($powerRanges) - 1; $i++) {
            $result[$powerRanges[$i]] = "{$powerRanges[$i]} - {$powerRanges[$i+1]} kWp";
        }

        return $this->json($result);
    }

    /**
     * @Route("/{id}/normalize", name="memorial_normalize_ranges")
     * @Method("post")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postMemorialNormalizeAction(Memorial $memorial)
    {
        /** @var \AppBundle\Service\Precifier\RangeNormalizer $rangeNormalizer */
        $rangeNormalizer = $this->get('precifier_range_normalizer');

        $rangeNormalizer->normalize($memorial);

        return $this->json();
    }

    /**
     * @Route("/levels", name="memorial_account_levels")
     * @Method("get")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemorialLevelsAction()
    {
        return $this->json(Memorial::getDefaultLevels());
    }

    /**
     * @Route("/{id}/copy_level", name="memorial_copy_level_ranges")
     * @Method("put")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putMemorialCopyLevelAction(Request $request, Memorial $memorial)
    {
        $source = $request->get('source');
        $target = $request->get('target');

        /** @var MemorialCloner $memorialCloner */
        $memorialCloner = $this->get('precifier_memorial_cloner');

        $memorialCloner->copyLevel($memorial, $source, $target);

        return $this->json();
    }

    /**
     * @Route("/{id}/clone", name="memorial_clone")
     * @Method("post")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postMemorialCloneAction(Memorial $memorial)
    {
        /** @var MemorialCloner $memorialCloner */
        $memorialCloner = $this->get('precifier_memorial_cloner');

        $memorialCloner->execute($memorial);

        return $this->json();
    }

    /**
     * @Route("/{id}/status", name="memorial_statuses")
     * @Method("get")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemorialStatusesAction(Memorial $memorial)
    {
        if ($memorial->canChangeStatus()) {
            return $this->json(Memorial::getDefaultStatuses());
        }

        return $this->json();
    }

    /**
     * @Route("/", name="list_memorials")
     * @Method("get")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemorialsAction(Request $request)
    {
        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('precifier_memorial_manager');

        $qb = $memorialManager->createQueryBuilder();
        $qb->orderBy('m.createdAt', 'DESC');

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
        $memorialManager = $this->get('precifier_memorial_manager');

        $data = json_decode($request->getContent(), true);

        /** @var Memorial $memorial */
        $memorial = $memorialManager->create();

        $memorial->setName($data['name']);

        $memorialManager->save($memorial);

        return $this->json([
            'id' => $memorial->getId()
        ]);
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
        $memorialManager = $this->get('precifier_memorial_manager');

        $data = json_decode($request->getContent(), true);

        $memorial->setName($data['name']);

        if ($memorial->canChangeStatus()) {
            $memorial->setStatus($data['status']);

            if ($memorial->isPublished()) {
                /** @var MemorialHelper $memorialHelper */
                $memorialHelper = $this->get('precifier_memorial_helper');

                $memorialHelper->syncPublishMemorial($memorial);
            }
        }

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
        if (!$memorial->isPending()) {
            return $this->json([
                'error' => 'Somente memoriais pendentes podem ser excluídos'
            ], Response::HTTP_CONFLICT);
        }

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->get('precifier_memorial_manager');

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
        $createdAt = $memorial->getCreatedAt() ? $memorial->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $publishedAt = $memorial->getPublishedAt() ? $memorial->getPublishedAt()->format('Y-m-d H:i:s') : null;
        $expiredAt = $memorial->getExpiredAt() ? $memorial->getExpiredAt()->format('Y-m-d H:i:s') : null;

        return [
            'id' => $memorial->getId(),
            'name' => $memorial->getName(),
            'createdAt' => $createdAt,
            'publishedAt' => $publishedAt,
            'expiredAt' => $expiredAt,
            'status' => $memorial->getStatus()
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

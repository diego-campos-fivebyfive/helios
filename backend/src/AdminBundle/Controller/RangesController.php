<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\RangeManager;
use AppBundle\Service\Precifier\RangeHelper;
use AppBundle\Service\Precifier\RangeNormalizer;
use AppBundle\Service\Precifier\RangePrecify;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("/api/v1/memorial_ranges")
 */
class RangesController extends AbstractController
{
    /**
     * @Route("/{id}", name="list_ranges")
     * @Method("get")
     * @param Request $request
     * @param Memorial $memorial
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function getRangesAction(Request $request, Memorial $memorial)
    {
        /** @var RangeNormalizer $rangeNormalizer */
        $rangeNormalizer = $this->container->get('precifier_range_normalizer');

        $rangeNormalizer->normalize($memorial);

        /** @var RangeHelper $rangeHelper */
        $rangeHelper = $this->container->get('precifier_range_helper');

        $ranges = $rangeHelper->filterAndFormatRanges($memorial, $request->query->all());

        return $this->json($ranges);
    }

    /**
     * @Route("/{id}/cost_price", name="update_cost_price_range")
     * @Method("put")
     * @param Request $request
     * @param Range $range
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putCostPriceRangeAction(Request $request, Range $range)
    {
        /** @var RangeManager $manager */
        $manager = $this->manager('precifier_range');

        $level = $request->request->get('level');

        $costPrice = (float) $request->request->get('costPrice');

        $rangesPrecification = RangePrecify::calculate($range->getMetadata(), $costPrice);

        $range->setCostPrice($costPrice);

        $range->setMetadata($rangesPrecification);

        $manager->save($range);

        return $this->json([
            'id' => $range->getId(),
            'costPrice' => $range->getCostPrice(),
            'powerRanges' => $range->getMetadata()[$level],
            'family' => $range->getFamily()
        ]);
    }

    /**
     * @Route("/{id}/markup", name="update_markup_range")
     * @Method("put")
     * @param Request $request
     * @param Range $range
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function putMarkupRangeAction(Request $request, Range $range)
    {
        /** @var RangeManager $manager */
        $manager = $this->manager('precifier_range');

        $markup = (float) $request->request->get('markup') / 100;
        $powerRange = (int) $request->request->get('powerRange');
        $isParent = (bool) $request->request->get('parent');
        $children = $request->request->get('children');
        $level = $request->request->get('level');

        $this->updateMarkup($range, $level, $markup, $powerRange);

        $manager->save($range, false);

        $results = [
            'level' => $level,
            'powerRange' => $powerRange,
            'family' => $range->getFamily(),
            'ranges' => []
        ];

        $this->formatRange($results, $range, $level, $powerRange);

        if ($isParent && $children) {

            /** @var RangeHelper $rangeHelper */
            $rangeHelper = $this->container->get('precifier_range_helper');

            $childrenRanges = $rangeHelper->load($children);

            /** @var Range $childRange */
            foreach ($childrenRanges as $childRange) {
                $this->updateMarkup($childRange, $level, $markup, $powerRange);

                $manager->save($childRange, false);

                $this->formatRange($results, $childRange, $level, $powerRange);
            }
        }

        $manager->flush();

        return $this->json($results);
    }

    /**
     * @param $results
     * @param Range $range
     * @param $level
     * @param $powerRange
     */
    private function formatRange(&$results, Range $range, $level, $powerRange)
    {
        $results['ranges'][] = [
            'id' => $range->getId(),
            'price' => $range->getMetadata()[$level][$powerRange]['price']
        ];
    }

    /**
     * @param Range $range
     * @param $level
     * @param $markup
     * @param $powerRange
     */
    private function updateMarkup(Range $range, $level, $markup, $powerRange)
    {
        $costPrice = $range->getCostPrice();

        $rangesPrecification = RangePrecify::calculate($range->getMetadata(), $costPrice, $level, $markup, $powerRange);

        $range->setMetadata($rangesPrecification);
    }
}

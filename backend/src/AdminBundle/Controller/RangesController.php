<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Service\Precifier\ComponentsLoader;
use AppBundle\Service\Precifier\RangeHelper;
use AppBundle\Service\Precifier\RangeNormalizer;
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
}

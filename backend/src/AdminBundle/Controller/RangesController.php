<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\RangeManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("/api/v1/ranges")
 */
class RangesController extends AbstractController
{
    /**
     * @Route("/{id}", name="update_range")
     * //@Method("put")
     * @param Request $request
     * @param Range $range
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putRangeAction(Request $request, Range $range)
    {
        /** @var RangeManager $rangeManager */
        $rangeManager = $this->get('precifier_range_manager');

        //mudar para request
        $costPrice = $request->query->get('cost_price');
        $powerRange = $request->query->get('power_range');
        $markup = $request->query->get('markup');

        dump($costPrice, $powerRange, $markup);die;

        //chamada ao serviço que faz o calculo do valor

        $rangeManager->save($range);

        return $this->json();
    }
}

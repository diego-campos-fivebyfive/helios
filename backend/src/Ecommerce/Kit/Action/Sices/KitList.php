<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Kit\Action\Sices;

use AppBundle\Controller\AbstractController;
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};
use Symfony\Component\HttpFoundation\Request;

class KitList extends AbstractController
{
    /**
     * @Route("/", name="kits_index")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');
        $page = $request->query->getInt('page', 1);
        $actives = $request->get('actives');
        $pagination = $kitService->findAll($actives, $page);

        return $this->render('ecommerce/kit/sices/index.html.twig', [
            'pagination' => $pagination,
            'kits_active_val' => $actives
        ]);
    }
}

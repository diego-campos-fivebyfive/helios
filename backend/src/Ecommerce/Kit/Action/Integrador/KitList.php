<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Kit\Action\Integrador;

use AppBundle\Controller\AbstractController;
use Ecommerce\Kit\Service\Integrador\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};
use Symfony\Component\HttpFoundation\Request;

class KitList extends AbstractController
{
    /**
     * @Route("/", name="index_kit")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('integrador.kit_service');
        $page = $request->query->getInt('page', 1);
        $pagination = $kitService->findAll($page);

        return $this->render('ecommerce/kit/integrador/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}

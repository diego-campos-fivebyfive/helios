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
use Ecommerce\Kit\Form\KitType;
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};
use Symfony\Component\HttpFoundation\Request;

class KitCreate extends AbstractController
{
    /**
     * @Route("/create", name="create_kit")
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
        $kit = $kitService->getModel();

        $form = $this->createForm(KitType::class, $kit);

        return $this->render("ecommerce/kit/sices/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $kitService->getComponentFamilies()
        ]);
    }
}

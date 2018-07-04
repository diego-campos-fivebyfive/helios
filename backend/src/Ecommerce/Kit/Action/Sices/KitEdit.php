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
use Ecommerce\Kit\Entity\Kit;
use Ecommerce\Kit\Form\KitType;
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};

class KitEdit extends AbstractController
{
    /**
     * @Route("/{id}/update", name="update_kit")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Method("get")
     *
     * @param Kit $kit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Kit $kit)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');
        $form = $this->createForm(KitType::class, $kit);

        return $this->render("ecommerce/kit/sices/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $kitService->getComponentFamilies()
        ]);
    }
}

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
use AppBundle\Service\Common\FlashMessage;
use Ecommerce\Kit\Form\KitType;
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};
use Symfony\Component\HttpFoundation\Request;

class KitSave extends AbstractController
{
    /**
     * @Route("/create")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Method("post")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function __invoke(Request $request)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');
        $kit = $kitService->getModel();

        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->files;
            $components = $form->get('components')->getData() ?? [];
            $kitService->save($kit, $components, $files);
            $this->setNotice('Kit cadastrado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        $this->setNotice('Falha ao cadastrar kit!', FlashMessage::ERROR);

        return $this->render("ecommerce/kit/sices/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $kitService->getComponentFamilies()
        ]);
    }
}

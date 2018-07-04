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
use Ecommerce\Kit\Entity\Kit;
use Ecommerce\Kit\Form\KitType;
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};
use Symfony\Component\HttpFoundation\Request;

class KitUpdate extends AbstractController
{
    /**
     * @Route("/{id}/update")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Method("post")
     *
     * @param Request $request
     * @param Kit $kit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function __invoke(Request $request, Kit $kit)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');

        $form = $this->createForm(KitType::class, $kit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->files;
            $components = $form->get('components')->getData() ?? [];
            $kitService->save($kit, $components, $files);
            $this->setNotice('Kit atualizado com sucesso!');

            return $this->redirectToRoute('kits_index');
        }

        $this->setNotice('Falha ao atualizar kit!', FlashMessage::ERROR);

        return $this->render("ecommerce/kit/sices/config.html.twig", [
            'form' => $form->createView(),
            'kit' => $kit,
            'families' => $kitService->getComponentFamilies()
        ]);
    }
}

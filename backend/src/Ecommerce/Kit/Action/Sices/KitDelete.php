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
use Ecommerce\Kit\Service\Sices\KitService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method, Security};

class KitDelete extends AbstractController
{
    /**
     * @Route("/{id}/delete", name="delete_kit")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     * @Method("delete")
     *
     * @param Kit $kit
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Kit $kit)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');
        $kitService->delete($kit);

        return $this->json(['message' => 'Kit excluído com sucesso']);
    }
}

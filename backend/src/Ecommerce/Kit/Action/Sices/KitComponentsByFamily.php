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
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class KitComponentsByFamily extends AbstractController
{
    /**
     * @Route("/components/{family}", name="kit_components_by_family")
     *
     * @param string $family
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(string $family)
    {
        /** @var KitService $kitService */
        $kitService = $this->get('sices.kit_service');

        if (! in_array($family, $kitService->getComponentFamilies())) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $manager = $this->manager($family);
        $components = $kitService->getComponentsByFamily($family, $manager);

        return $this->json($components, Response::HTTP_OK);
    }
}

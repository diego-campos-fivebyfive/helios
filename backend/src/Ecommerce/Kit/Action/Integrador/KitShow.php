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
use Ecommerce\Kit\Entity\Kit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method};

class KitShow extends AbstractController
{
    /**
     * @Route("/{id}", name="kit_show")
     * @Method("GET")
     *
     * @param Kit $kit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Kit $kit)
    {
        return $this->render('ecommerce/kit/show.html.twig', [
            'kit' => $kit
        ]);
    }
}

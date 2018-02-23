<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("fiscal")
 */
class FiscalController extends AbstractController
{
    /**
     * @Route("/danfe")
     */
    public function danfeAction()
    {
        $this->get('nfe_core')->core();
    }
}

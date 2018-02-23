<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 2/23/18
 * Time: 11:34 AM
 */

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

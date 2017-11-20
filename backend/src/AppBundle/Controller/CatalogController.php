<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("catalog")
 * @Breadcrumb("Lista de Sistemas")
 */
class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="index_catalog")
     */
    public function indexAction(Request $request)
    {
        return $this->render('catalog/index.html.twig', array(
        ));
    }
}

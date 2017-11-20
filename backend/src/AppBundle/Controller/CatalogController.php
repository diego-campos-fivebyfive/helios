<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("catalog")
 * @Breadcrumb("Lista de Sistemas")
 */
class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="index_catalog")
     */
    public function indexAction()
    {
        return $this->render('catalog/index.html.twig', []);
    }
}

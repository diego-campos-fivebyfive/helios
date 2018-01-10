<?php

namespace AppBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/template")
 */
class TemplateController extends AbstractController
{
    /**
     * @Breadcrumb("Templates")
     * @Route("/", name="template")
     */
    public function indexAction()
    {
        return $this->render('projects/templates/index.html.twig');
    }
}

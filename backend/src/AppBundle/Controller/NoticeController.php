<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class NoticeController
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 *
 * @Route("notice")
 *
 * @Breadcrumb("Notice")
 */
class NoticeController extends AbstractController
{
    /**
     * @Route("/{view}", name="notice_render")
     * @Method("get")
     */
    public function renderAction($view)
    {
        return $this->render('notices/'.$view.'.html.twig');
    }
}

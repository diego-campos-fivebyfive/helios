<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontendRenderController extends Controller
{
    public function renderAction()
    {
        $kernel = $this->get('kernel');

        $projectRoot = "{$kernel->getRootDir()}/..";

        $content = file_get_contents("{$projectRoot}/web/app/index.html");

        return new Response($content);
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Menu\Skeleton;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("application")
 */
class ApplicationController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     */
    public function menuAction()
    {
        $menu = new Skeleton();
        $menu->setContainer($this->container);

        return $this->json($menu->getMenuSkeleton());
    }
}

<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\AdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 *
 * @Route("metrics")
 *
 */
class MetricsController extends AdminController
{
    /**
     * GET Github Metrics View
     *
     * @Route("/", name="metrics_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/metrics/index.html.twig', []);
    }

    /**
     * GET Github Metrics
     *
     * @Route("/api/v1/modules", name="metrics_module")
     * @Method("GET")
     */
    public function getModules()
    {
        $mock = [
            0 => [
                "name" => "Orçamento",
                "type" => "Plataforma",
                "closed" => 100,
                "open" => 5
            ]
        ];

        return $this->json($mock);
    }
}

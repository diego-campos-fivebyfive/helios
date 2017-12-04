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
        $user = '';
        $token = '';

        $options = [
            'url' => $this->getParameter('github_repository') . "/milestones",
            'agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
            'login' => "${user}:${token}"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $options['url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $options['login']);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERAGENT, $options['agent']);
        $response = curl_exec($curl);
        curl_close($curl);

        return $this->json(json_decode($response));
    }
}

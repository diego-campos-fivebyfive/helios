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
        $root = $this->get('kernel')->getRootDir() . "/../..";
        $temp = "${root}/devops/cli/stash/temp";

        $file = fopen("${temp}/.ces-credentials", "r");
        $size = filesize("${temp}/.ces-credentials");

        $credentials = explode("\n", fread($file, $size));

        fclose($file);

        $options = [
            'url' => $this->getParameter('github_repository') . "/milestones",
            'agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
            'login' => "${credentials[0]}:${credentials[1]}"
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $options['url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $options['login']);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERAGENT, $options['agent']);

        $milestones = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $modules = array_map(function($milestone) {
            return [
                "title" => $milestone['title'],
                "open" => $milestone['open_issues'],
                "closed" => $milestone['closed_issues']
            ];
        }, $milestones);

        return $this->json($modules);
    }
}

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
     * @var userAgent
     */
    private $userAgent;

    /**
     * MetricsController constructor
     */
    function __construct()
    {
        $this->userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    }

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
     * @return mixed
     */
    private function getRepositoryCredentials()
    {
        $root = $this->get('kernel')->getRootDir() . "/../..";
        $temp = "${root}/devops/cli/stash/temp";

        $file = fopen("${temp}/.ces-credentials", "r");
        $size = filesize("${temp}/.ces-credentials");

        $credentials = explode("\n", fread($file, $size));

        fclose($file);

        return "${credentials[0]}:${credentials[1]}";
    }

    /**
     * GET Github Milestones
     *
     * @Route("/api/v1/milestones", name="metrics_milestones")
     * @Method("GET")
     */
    public function getMilestones()
    {
        $uri = $this->getParameter('github_repository') . "/milestones";

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, self::getRepositoryCredentials());
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

        $milestones = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $milestones = array_map(function($milestone) {

            $total = $milestone['open_issues'] + $milestone['closed_issues'];
            $average = intval($milestone['closed_issues'] / $total * 100);

            return [
                "id" => $milestone['number'],
                "title" => $milestone['title'],
                "open" => $milestone['open_issues'],
                "closed" => $milestone['closed_issues'],
                "total" => $total,
                "average" => $average
            ];

        }, $milestones);

        usort($milestones, function($a, $b) {
            return strcmp($b['open'], $a['open']);
        });

        return $this->json($milestones);
    }

    /**
     * GET Github Issues by Milestone
     *
     * @Route("/api/v1/milestones/{id}/issues", name="metrics_milestone")
     * @Method("GET")
     */
    public function getMilestone($id)
    {
        $query = [
          'query' => " query {
            repository(owner:\"sices\", name:\"sices\") {
              milestone(number: ${id}) {
                issues(first: 100) {
                  edges {
                    node {
                      title
                    }
                  }
                }
              }
            }
          }"
        ];

        $uri = 'https://api.github.com/graphql';
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, self::getRepositoryCredentials());
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($query));

        $response = json_decode(curl_exec($curl), true);
        $milestone = $response['data']['repository']['milestone'];
        $issuesNode = $milestone['issues']['edges'];

        $issues = array_map(function($issue) {
            return $issue['node'];
        }, $issuesNode);

        curl_close($curl);

        return $this->json($issues);
    }
}

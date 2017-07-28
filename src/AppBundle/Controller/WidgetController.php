<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Component\ProjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("widgets")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/{widget}/load", name="widget_load")
     */
    public function loadAction($widget)
    {
        switch ($widget) {
            case 'sale-stages':
                return $this->saleStagesWidget();
                break;
            case 'timeline':
                return $this->timelineWidget();
                break;
        }

        return $this->render(sprintf('widget.%s', $widget));
    }

    /**
     * @Route("/proposal", name="widget_proposal")
     */
    public function proposalAction(Request $request)
    {
        $today = new \DateTime;

        $group = $request->get('group', 'month');
        $year = $request->get('year', $today->format('Y'));
        $month = $request->get('month', $today->format('m'));
        $day = $today->format('d');

        //$filter = $this->getProposalFilter();

        $member = $this->member();

        $date = new \DateTime(sprintf('%s-%s-%s', $year, $month, $day));
        $lastDay = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));

        /*$filter
            ->date($date)
            ->at($group);*/

        if (!$member->isAdmin()) {
            if ($member->isOwner()) {
                //$filter->account($member->getAccount());
            } else {
                //$filter->member($member);
            }
        }

        //$data = $filter->get();

        //$this->dd($data);

        // Defaults
        $groups = [];
        $limit = 'month' == $group ? $lastDay : 12;
        for ($i = 1; $i <= $limit; $i++) {
            $groups[$i] = [
                'power' => 0,
                'amount' => 0,
                'count' => 0
            ];
        }

        $data = [];
        foreach ($data as $financial) {
            //if ($financial instanceof ProjectFinancialInterface && $financial->isIssued()) {
                $project = $financial->getProject();

                /**
                 * TODO - $financial->isIssued()
                 * This method cause a performance impact considerable,
                 * because this same check is possible via database query,
                 * before this statement, with reduced data quantity.
                 * TODO
                 * Check method WidgetController::summaryAction()
                 */

                $index = 'month' == $group
                    ? (int)$financial->getCreatedAt()->format('d')
                    : (int)$financial->getCreatedAt()->format('m');

                $groups[$index]['count'] += 1;
                $groups[$index]['power'] += $project->getPower();
                $groups[$index]['amount'] += $project->getPrice();

            //}
        }

        return $this->json([
            'options' => [
                'last_day' => $lastDay
            ],
            'data' => $groups
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/summary", name="widget_summary")
     */
    public function summaryAction()
    {
        $member = $this->member();
        //$filter = $this->getProposalFilter();

        if (!$member->isAdmin()) {
            if ($member->isOwner()) {
                //$filter->account($member->getAccount());
            } else {
                //$filter->member($member);
            }
        }

        //$data = $filter->get();

        $summary = [
            'count' => 0,
            'amount' => 0,
            'power' => 0
        ];
        /*foreach ($data as $financial) {
            if ($financial instanceof ProjectFinancialInterface && $financial->isIssued()) {

                $project = $financial->getProject();

                $summary['count'] += 1;
                $summary['amount'] += $project->getPrice();
                $summary['power'] += $project->getPower();
            }
        }*/

        return $this->json([
            'data' => $summary
        ], Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    private function saleStagesWidget()
    {
        $projectManager = $this->getProjectManager();

        $member = $this->getCurrentMember();

        if ($member->isOwner()) {
            $projects = $projectManager->findByAccount($member->getAccount());
        } else {
            $projects = $member->getProjects()->toArray();
        }

        $stages = $this->getCategoryManager()->findBy([
            'account' => $member->getAccount(),
            'context' => CategoryInterface::CONTEXT_SALE_STAGE
        ]);

        /**
         * Create stage collection
         */
        $collection = [];
        foreach ($stages as $stage) {
            if ($stage instanceof CategoryInterface) {
                if (!array_key_exists($stage->getId(), $collection)) {
                    $collection[$stage->getId()] = [
                        'stage' => $stage,
                        'count' => 0,
                        'amount' => 0,
                        'power' => 0
                    ];
                }
            }
        }

        /**
         * Fetch projects
         */
        foreach ($projects as $project) {
            if ($project instanceof ProjectInterface) {

                $stageId = $project->getSaleStage()->getId();

                $collection[$stageId]['count'] += 1;
                $collection[$stageId]['power'] += $project->getPower();
                $collection[$stageId]['amount'] += $project->getPrice();
            }
        }

        /**
         * Sort positions
         */
        uasort($collection, function ($a, $b) {
            /**
             * @var CategoryInterface $stageA
             * @var CategoryInterface $stageB
             */
            $stageA = $a['stage'];
            $stageB = $b['stage'];

            return $stageA->getPosition() > $stageB->getPosition();
        });

        return $this->render('widget.sale-stages', [
            'collection' => $collection
        ]);
    }

    /**
     * @return Response
     */
    private function timelineWidget()
    {
        $member = $this->getCurrentMember();

        $subscriptions = $this->get('app.notification_manager')->subscriptions($member, [
            'type' => Notification::TYPE_TIMELINE,
            'limit' => 6
        ]);

        return $this->render('widget.timeline', [
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * @return \AppBundle\Service\ProposalFilter|object
     */
    private function getProposalFilter()
    {
        return $this->get('app.proposal_filter');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\CategoryManager|object
     */
   /* private function getCategoryManager()
    {
        return $this->get('sonata.classification.manager.category');
    }*/
}
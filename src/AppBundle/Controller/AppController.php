<?php

namespace AppBundle\Controller;

use AppBundle\Configuration\App;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Service\WidgetGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @Breadcrumb("Dashboard")
     */
    public function indexAction(Request $request)
    {
        //($this->getCurrentAccount()->getProjects()->count());

        $this->getTopbarAction('app_index');
        
        $member = $this->getCurrentMember();

        return $this->render('dashboard.default', array(
            'member' => $member,
            'woopraEvent' => $this->requestWoopraEvent($request)
        ));
    }

    /**
     * @Route("/check-online", name="app_check_online")
     */
    public function checkOnlineAction()
    {
        $user = $this->getUser();
        $online = $user->isOnline();

        if(!$user->isOnline()){
            $user->setLastActivity(new \DateTime);
            $this->getUserManager()->updateUser($user);
        }

        return $this->jsonResponse([
            'online' => $online
        ]);
    }

    /**
     * @Route("lock_screen", name="lock_screen")
     */
    public function lockScreenAction()
    {
        $member = $this->getCurrentMember();

        return $this->render('app.lockscreen', [
            'member' => $member
        ]);
    }

    /**
     * @param null $route
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTopbarAction($route, array $parameters = [])
    {
        $trail = $this->container->get("apy_breadcrumb_trail");

        $breadcrumbs = $trail->getIterator();

        $buttons = App::getTopbarButtons($route);
        
        $title = 'Dashboard';
        foreach($breadcrumbs as $breadcrumb){
            $title = $breadcrumb->title;
        }

        return $this->render('app.topbar', [
            'route' => $route,
            'title' => $title,
            'buttons' => $buttons,
            'parameters' => $parameters
        ]);
    }

    /**
     * @Route("/woopra", name="app_woopra")
     */
    public function woopraAction($id)
    {
        /** @var \AppBundle\Service\Woopra\Manager $manager */
        $manager = $this->get('app.woopra_manager');

        /** @var \AppBundle\Service\Woopra\Event $event */
        $event = $manager->getEvent($id);

        if($event) {
            $manager->deleteEvent($event);
        }

        return $this->render('helper.woopra', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/intercom", name="app_intercom")
     */
    public function intercomAction()
    {
        $member = $this->getCurrentMember();
        $account = $member->getAccount();
        $filter = $this->getProposalFilter();

        if (!$member->isAdmin()) {
            if ($member->isOwner()) {
                $filter->account($member->getAccount());
            } else {
                $filter->member($member);
            }
        }

        $data = array_filter($filter->get(), function(ProjectFinancialInterface $financial){
            return $financial->isIssued();
        });

        $contacts = $member->isOwner() ? $member->getAccountContacts() : $member->getContacts() ;

        $tasks = $member->getAssignedTasks();
        $projects = $member->isOwner() ? $member->getAccount()->getProjects() : $member->getProjects();

        /*if(!$account->isLocked()) {
            $plan = 'Trial';
            $signature = $account->getSignature();
            if(null != $signature['subscription']){
                $plan = 'Assinante';
            }
        }*/

        $plan = 'Trial';
        if(!$account->isFreeAccount()){
            $plan = 'Assinante';
        }

        return $this->render('helper.intercom', [
            'member' => $member,
            'proposals' => count($data),
            'projects' => $projects->count(),
            'tasks' => $tasks->count(),
            'contacts' => $contacts->count(),
            'plan' => $plan,
            'month_projects' => $account->getProjectsCount()
        ]);
    }


    /**
     * @Route("/widget/{widget}", name="app_widget")
     */
    public function widgetAction(Request $request, $widget)
    {
        $member = $this->getCurrentMember();

        /** @var WidgetGenerator $generator */
        $generator = $this->get('app.widget_generator');

        $generator->member($member);

        $widget =  $generator->generate($widget);

        return $this->render(sprintf('widget.%s', $widget->view),[
            'widget' => $widget
        ]);
    }

    /**
     * @return \AppBundle\Service\ProposalFilter|object
     */
    private function getProposalFilter()
    {
        return $this->get('app.proposal_filter');
    }
}
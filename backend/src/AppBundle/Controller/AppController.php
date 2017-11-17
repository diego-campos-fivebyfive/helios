<?php

namespace AppBundle\Controller;

use AppBundle\Configuration\App;
use AppBundle\Service\Business\DataCollector;
use AppBundle\Service\Business\Intercom;
use AppBundle\Service\WidgetGenerator;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @Breadcrumb("Dashboard")
     */
    public function indexAction(Request $request)
    {
        if(!$this->account()->isActivated()){
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $this->getTopbarAction('app_index');
        
        $member = $this->member();

        if ($member->isPlatformUser()) {
            return $this->render('admin/dashboard/index.html.twig', array(
                'member' => $member,
                'woopraEvent' => $this->requestWoopraEvent($request)
            ));
        } else {
            return $this->render('dashboard.default', array(
                'member' => $member,
                'woopraEvent' => $this->requestWoopraEvent($request)
            ));
        }
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
     * @deprecated
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
        $collector = DataCollector::create($this->container);

        $data = array_merge(['app_id' => 't2yycetv'], $collector->get('data'));

        return $this->render('helper.intercom', [
            'data' => $data
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

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Form\TaskFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\TaskType;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Security("has_role('ROLE_USER')")
 *
 * @Route("tasks")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Tasks", route={"name"="task_index"})
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/m/{mode}", name="task_index", defaults={"mode":"list"})
     * @Method({"get", "post"})
     */
    public function indexAction(Request $request, $mode)
    {
        if($request->isXmlHttpRequest()){

            $response = 'calendar' == $mode ? $this->debugAction($request) : $this->getTasksAction($request) ;

            return $response;
        }

        return $this->render('task.index', [
            'mode' => $mode
        ]);
    }

    /**
     * @Route("/filter/{mode}", name="task_filter_form")
     */
    public function getFilterAction(Request $request, $mode)
    {
        $form = $this->createFormFilter([
            'contact' => $request->get('contact')
        ]);

        return $this->render('task.filters', [
            'types' => Task::getTypes(),
            'icons' => Task::getTypeIcons(),
            'form' => $form->createView(),
            'mode' => $mode,
            'origin' => $request->get('origin')
        ]);
    }

    /**
     * @Route("/{token}/show", name="task_show")
     */
    public function showAction(Request $request, Task $task)
    {
        if ($request->isXmlHttpRequest()) {

            return $this->render('task._show', [
                'task' => $task
            ]);
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("/debug", name="task_debug")
     */
    public function debugAction(Request $request)
    {
        $formFilter = $this->createFormFilter();

        $formFilter->handleRequest($request);

        if ($formFilter->isSubmitted() && $formFilter->isValid()) {

            $data = $formFilter->getData();
            $filter = $this->getTaskFilter();

            if (!empty($data['status']))
                $filter->status($data['status']);

            if (!empty($data['type']))
                $filter->types($data['type']);

            $start = new \DateTime($request->get('start'));
            $end = new \DateTime($request->get('end'));

            $filter->member($this->getCurrentMember());

            $tasks = $filter->interval($start, $end)->get();

            $events = $this->formatTasksCalendar($tasks);

            return $this->jsonResponse($events);
        }

        return $this->jsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/all", name="tasks")
     */
    public function getTasksAction(Request $request)
    {
        $member = $this->getCurrentMember();
        $filter = $this->getTaskFilter();

        $filterForm = $this->createForm(TaskFilterType::class, null, [
            'method' => 'get'
        ]);

        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            if (!empty($data['status']))
                $filter->status($data['status']);

            if (!empty($data['type']))
                $filter->types($data['type']);

            if (!empty($data['date'])) {
                switch ($data['date']) {
                    case 'today':
                        $filter->date();
                        break;
                    case 'week':
                        $filter->week();
                        break;
                }
            }

            if(!empty($data['contact'])){
                if(null != $token = $data['contact']){
                    /** @var \AppBundle\Entity\BusinessInterface $contact */
                    $contact = $this->getCustomerManager()->findOneBy(['token' => $token]);
                    if($contact && $contact->isAccessibleBy($member)){
                        $filter->contact($contact);
                    }
                }
            }
        }

        $filter->member($member);

        $query = $filter->getQuery();

        $paginator = $this->getPaginator();

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 5, []);

        return $this->render('task.tasks', [
            'pagination' => $pagination,
            'types' => Task::getTypes(),
            'icons' => Task::getTypeIcons(),
            'form' => $filterForm->createView(),
            'mode' => 'list'
        ]);
    }

    /**
     * @Route("/count", name="tasks_count")
     */
    public function countAction()
    {
        $member = $this->getCurrentMember();

        $filter = $this->getTaskFilter();

        $filter
            ->member($member)
            ->status(Task::STATUS_ENABLED)
            ->interval($member->getCreatedAt(), new \DateTime)
        ;

        return $this->jsonResponse([
            'count' => count($filter->get())
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getTaskManager();
        $member = $this->getCurrentMember();

        /** @var TaskInterface $task */
        $task = $manager->create();
        $task->setAuthor($member);

        if(null != $contact = $this->getContactReferer($request)){
            $task->setContact($contact);
        }

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_create'),
            'manipulator' => $member
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Todo: Do not remove this code
             */
            $task->addMember($member);

            $manager->save($task);

            $this->getNotificationGenerator()->scheduledTask($task);

            return $this->jsonResponse([
                'task' => [
                    'id' => $task->getId(),
                    'token' => $task->getToken()
                ]
            ], Response::HTTP_CREATED);
        }

        return $this->render('task.form', [
            'task' => $task,
            'form' => $form->createView(),
            'icons' => Task::getTypeIcons()
        ]);
    }

    /**
     * @Route("/{token}/update", name="task_update")
     */
    public function updateAction(Request $request, Task $task)
    {
        $this->checkAccess($task);

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $request->getUri(),
            'manipulator' => $this->getCurrentMember()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Todo: Do not remove this code
             */
            $task->addMember($this->getCurrentMember());

            $this->getTaskManager()->save($task);

            return $this->jsonResponse([
                'task' => [
                    'id' => $task->getId(),
                    'token' => $task->getToken()
                ]
            ], Response::HTTP_ACCEPTED);
        }

        $this->clearTemplateCache('task.form');

        return $this->render('task.form', [
            'task' => $task,
            'form' => $form->createView(),
            'icons' => Task::getTypeIcons()
        ]);
    }

    /**
     * @Route("/{token}/interval", name="task_interval")
     * @Method("post")
     */
    public function updateIntervalAction(Request $request, Task $task)
    {
        $this->checkAccess($task);

        $startAt = new \DateTime($request->request->get('start'));
        $endAt = new \DateTime($request->request->get('end'));

        $task
            ->setStartAt($startAt)
            ->setEndAt($endAt)
        ;

        $this->getTaskManager()->save($task);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/{token}/open", name="task_open")
     * @Method("post")
     */
    public function openAction(Task $task)
    {
        $this->checkAccess($task);

        $task->setStatus(Task::STATUS_ENABLED);

        $this->getTaskManager()->save($task);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/{token}/done", name="task_done")
     * @Method("post")
     */
    public function doneAction(Request $request, Task $task)
    {
        $this->checkAccess($task);

        $task->setStatus(Task::STATUS_DONE);

        $this->getTaskManager()->save($task);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/{token}/delete", name="task_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $this->checkAccess($task);

        $this->getTaskManager()->delete($task);

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createFormFilter(array $options = [])
    {
        $defaults = array_merge([
            'status' => Task::STATUS_ENABLED,
            'date' => 'all'
        ], $options);

        return $this->createForm(TaskFilterType::class, $defaults, [
            'method' => 'get'
        ]);
    }

    /**
     * @return \AppBundle\Service\TaskFilter|object
     */
    private function getTaskFilter()
    {
        return $this->get('app.task_filter');
    }

    /**
     * @param TaskInterface $task
     */
    private function checkAccess(TaskInterface $task)
    {
        $member = $this->getCurrentMember();

        if (!$task->getMembers()->contains($member)) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param array $tasks
     * @property Task $task
     * @return array
     */
    private function formatTasksCalendar(array $tasks)
    {
        $data = [];
        foreach($tasks as $task){
            $data[] = [
                'id' => $task->getToken(),
                'title' => $task->getDescription(),
                'start' => str_replace(' ', 'T', $task->getStartAt()->format('Y-m-d H:i:s')),
                'end' => str_replace(' ', 'T', $task->getEndAt()->format('Y-m-d H:i:s'))
            ];
        }

        return $data;
    }

    /**
     * @param Request $request
     * @return BusinessInterface|null
     */
    private function getContactReferer(Request $request)
    {
        $referer = $request->server->get('HTTP_REFERER');

        $token = $this->restore($referer, null, false);

        if($token){
            /** @var \AppBundle\Entity\BusinessInterface $contact */
            $contact = $this->getCustomerManager()->findOneBy(['token' => $token]);

            if($contact->isAccessibleBy($this->getCurrentMember())){
                return $contact;
            }
        }

        return null;
    }
}

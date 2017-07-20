<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectAreaInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectInverterInterface;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Form\Component\ProjectAreaType;
use AppBundle\Form\Component\ProjectType;
use AppBundle\Form\Project\ProjectInverterType;
use AppBundle\Service\ProjectHelper;
use AppBundle\Service\ProjectProcessor;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\MemberInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class ProjectController
 * @Route("project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index")
     */
    public function indexAction(Request $request)
    {
        $member = $this->member();
        $account = $member->getAccount();

        $ids = [$member->getId()];

        if ($member->isOwner()) {
            $ids = $account->getMembers()->map(function (MemberInterface $m) {
                return $m->getId();
            })->toArray();
        }

        $filterMember = null;
        if($member->isOwner() && null != $memberId = $request->get('member')){
            $filterMember = $this->getCustomerManager()->find($memberId);
            if($filterMember instanceof BusinessInterface
                && $filterMember->getAccount()->getId() == $account->getId()){
                $ids = [$filterMember->getId()];
            }
        }

        $qb = $this->manager('project')->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->manager('project')->getClass(), 'p')
            ->join('p.customer', 'c', 'WITH')
            ->join('p.stage', 's')
            ->where($qb->expr()->in('p.member', $ids))
            ->orderBy('p.number', 'desc');

        if(null != $customer = $this->getCustomerReferrer($request)){
            $qb->andWhere('p.customer = :customer')->setParameter('customer', $customer);
        }

        if(null != $stage = $request->get('stage', null)){
            $qb->andWhere('s.id = :stage');
            $qb->setParameter('stage', $stage);
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            8
        );

        $stages = $this->manager('category')->findBy([
            'context' => Category::CONTEXT_SALE_STAGE,
            'account' => $account
        ],['position' => 'asc']);

        $view = $request->isXmlHttpRequest() ? 'project.index_content' : 'project.index';

        return $this->render($view, [
            'current_stage' => $stage,
            'stages' => $stages,
            'pagination' => $pagination,
            'customer' => $customer,
            'current_member' => $filterMember
        ]);
    }

    /**
     * @Route("/create", name="project_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('project');

        /** @var Project $project */
        $project = $manager->create();

        $project->setMember($this->member());

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var \AppBundle\Entity\Component\ModuleInterface $module */
            $module = $this->manager('module')->find(32433);
            //$kwh = (int) $request->request->get('consumption');
            $project->setInfPower((int) $project->getInfConsumption());

            $power = $this->get('power_estimator')->estimate($project->getInfPower(), $project->getLatitude(), $project->getLongitude());
            $inverters = $this->get('inverter_combinator')->combine($module, $power, 60627);

            $stringBoxes = $this->get('string_box_calculator')->calculate($inverters);

            /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $projectGenerator */
            $projectGenerator = $this->get('project_generator');
            $projectGenerator->project($project);

            $project = $projectGenerator->fromCombination([
                'inverters' => $inverters,
                'module' => $module,
                'string_boxes' => $stringBoxes
            ]);

            $this->get('structure_calculator')->calculate($project);

            $this->get('project_manipulator')->generateAreas($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId()
                ]
            ]);
        }

        return $this->render('project.form_project', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/create/multi",name="project_create_multi")
     */
    public function multiCreateAction()
    {
        return $this->render('');
    }

    /**
     * @Route("/{id}/update", name="project_update")
     */
    public function updateAction(Request $request, Project $project)
    {
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            return $this->json([
                'project' => [
                    'id' => $project->getId()
                ]
            ]);
        }

        return $this->render('project.form_project', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/components", name="project_components")
     */
    public function componentsAction(Project $project)
    {
        return $this->render('project.components', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/groups", name="project_groups")
     */
    public function groupsAction(Request $request, Project $project)
    {
        if($request->isMethod('post')){

            /** @var ProjectModule $projectModule */
            $projectModule = $project->getProjectModules()->first();

            $groups = $request->request->get('groups');

            $projectModule->setGroups($groups);

            $this->manager('project_module')->save($projectModule);

            $this->get('structure_calculator')->calculate($project);

            return $this->json();
        }

        return $this->render('project.form_groups', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/process", name="project_process")
     */
    public function processAction(Request $request, Project $project)
    {
        if($project->isComputable()) {
            /** @var ProjectHelper $helper */
            $helper = $this->get('app.project_helper');
            $helper->processProject($project);

            $metadata = $project->getMetadata();

            return $this->json([
                'data' => $project->getMonthlyProduction(),
                'info' => $this->renderView('project.info_processed', [
                    'general' => $metadata['total'],
                    'project' => $project
                ]),
                'warnings' => $this->renderView('project.info_warnings', [
                    'project' => $project
                ])
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}/inverters", name="project_inverters")
     */
    public function invertersAction(Project $project)
    {
        return $this->render('project.inverters', [
            'project' => $project
        ]);
    }

    /**
     * Update ProjectInverter config
     *
     * @Route("/inverters/{id}/update", name="project_inverter_update")
     */
    public function updateInverterAction(Request $request, ProjectInverter $projectInverter)
    {
        $form = $this->createForm(ProjectInverterType::class, $projectInverter, [
            'mppt_manager' => $this->manager('mppt')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager('project_inverter')->save($projectInverter);

            $this->onUpdateProjectInverter($projectInverter);

            return $this->json([]);
        }

        return  $this->render('project.form_inverter', [
            'form' => $form->createView(),
            'projectInverter' => $projectInverter
        ]);
    }

    /**
     * @Route("/inverters/{id}/metadata", name="project_inverter_metadata")
     */
    public function inverterMetadataAction(ProjectInverter $projectInverter)
    {
        return $this->json($projectInverter->getMetadata());
    }

    /**
     * @Route("/inverters/{id}/operation", name="project_inverter_operation")
     */
    public function operationInverterAction(ProjectInverter $projectInverter)
    {
        return $this->render('project.operation_inverter', [
            'projectInverter' => $projectInverter
        ]);
    }

    /**
     * @Route("/{id}/areas", name="project_areas")
     */
    public function areasAction(ProjectInverter $projectInverter)
    {
        return $this->render('project.areas', [
            'projectInverter' => $projectInverter
        ]);
    }

    /**
     * @Route("/areas/{id}/update", name="project_area_update")
     */
    public function updateAreaAction(Request $request, ProjectArea $projectArea)
    {
        $form = $this->createForm(ProjectAreaType::class, $projectArea);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var ProjectHelper $processor */
            $helper = $this->get('app.project_helper');
            $helper->debugArea($projectArea);

            $this->onUpdateProjectArea($projectArea);

            return $this->json([
                'module' => [
                    'power' => $projectArea->getPower(),
                    'area' => $projectArea->getArea()
                ]
            ]);
        }

        return $this->render('project.form_area', [
            'form' => $form->createView(),
            'projectArea' => $projectArea
        ]);
    }

    /**
     * @Route("/areas/{id}/operation", name="project_area_operation")
     */
    public function operationAreaAction(ProjectArea $projectArea)
    {
        /** @var ProjectHelper $processor */
        $helper = $this->get('app.project_helper');
        $helper->debugArea($projectArea);

        $this->clearTemplateCache('project.area_operation');

        return $this->render('project.area_operation', [
            'data' => $projectArea->getMetadata()
        ]);
    }

    /**
     * @Route("/{id}/check_step", name="project_check_step")
     */
    public function checkStepAction(Request $request, Project $project)
    {
        $status = 202;
        $error = null;

        return $this->json(['error' => $error], $status);
    }

    /**
     * @Route("/{id}/stage/{stage}", name="project_stage")
     * @Method("post")
     */
    public function stageAction(Project $project, Category $stage)
    {
        if($this->account() == $stage->getAccount()) {

            $project->setStage($stage);

            $this->manager('project')->save($project);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}/delete", name="project_delete")
     * @Method("delete")
     */
    public function deleteAction(Project $project)
    {
        //$this->checkAccess($project);

        $this->manager('project')->delete($project);

        return $this->json([]);
    }

    /**
     * @param ProjectInverter $projectInverter
     */
    private function resetProjectAreas(ProjectInverterInterface $projectInverter)
    {
        if($projectInverter->operationIsChanged()){

            $projectAreaManager = $this->manager('project_area');
            $projectAreas = $projectInverter->getProjectAreas();

            foreach($projectAreas as $key => $projectArea){
                $projectAreaManager->delete($projectArea, ($key == $projectAreas->count()-1));
            }

            /** @var \AppBundle\Entity\Project\MpptOperation $operation */
            $operation = $this->manager('mppt')->find($projectInverter->getOperation());

            for ($i = 0; $i < $operation->count(); $i++) {

                /** @var \AppBundle\Entity\Component\ProjectArea $projectArea */
                $projectArea = $projectAreaManager->create();

                $projectArea->setProjectInverter($projectInverter);

                $projectAreaManager->save($projectArea, ($i == $operation->count()-1));
            }
        }
    }

    /**
     * @param ProjectInverterInterface $projectInverter
     */
    private function onUpdateProjectInverter(ProjectInverterInterface $projectInverter)
    {
        $this->resetProjectAreas($projectInverter);

        $project = $projectInverter->getProject();

        $this->get('project_manipulator')->synchronize($project);
        $this->get('structure_calculator')->calculate($project);
    }

    /**
     * @param ProjectAreaInterface $projectArea
     */
    private function onUpdateProjectArea(ProjectAreaInterface $projectArea)
    {
        $project = $projectArea->getProjectModule()->getProject();

        $this->get('project_manipulator')->synchronize($project);
        $this->get('structure_calculator')->calculate($project);

    }

    /*
     * @param Request $request
     * @return BusinessInterface|null|object
     */
    private function getCustomerReferrer(Request $request)
    {
        if(null != $token = $request->get('contact')) {
            $contact = $this->getCustomerManager()->findByToken($token);

            $this->denyAccessUnlessGranted('edit', $contact);

            return $contact;
        }

        return null;
    }
}
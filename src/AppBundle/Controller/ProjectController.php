<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\MemberInterface;
use AppBundle\Form\Component\ProjectAreaType;
use AppBundle\Form\Component\ProjectType;
use AppBundle\Form\Component\GeneratorType;
use AppBundle\Form\Project\ProjectInverterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class ProjectController
 * @Route("project")
 * @Breadcrumb("Projetos")
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
     * @Route("/makers", name="detect_makers")
     */
    public function makersAction(Request $request)
    {
        $source = $request->request->get('source_option');
        $data = $request->request->get('generator');
        $power = (float) $data['power'];

        if('consumption' == $source){

            $consumption = (float) $data['consumption'];
            $latitude = (float) $data['latitude'];
            $longitude = (float) $data['longitude'];

            $power = $this->get('power_estimator')->estimate($consumption, $latitude, $longitude);
        }

        $makers = $this->get('maker_detector')->fromPower($power);

        $ids = array_map(function(MakerInterface $maker){
            return $maker->getId();
        }, $makers);

        return $this->json([
            'makers' => $ids
        ]);
    }

    /**
     * @Route("/create", name="project_create")
     * @Breadcrumb("Novo Projeto")
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

            $generator = $this->getGenerator();

            $defaults = $generator->loadDefaults([
                'roof_type' => $project->getRoofType(),
                'consumption' => $project->getInfConsumption(),
                'latitude' => $project->getLatitude(),
                'longitude' => $project->getLongitude()
            ]);

            $project->setDefaults($defaults);

            $generator->generate($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId(),
                    'power' => $project->getPower()
                ]
            ]);
        }

        return $this->render('project.form_project', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/update", name="project_update")
     */
    public function updateAction(Request $request, Project $project)
    {
        $previous = [
            'power' => $project->getInfPower(),
            'consumption' => $project->getInfConsumption(),
            'latitude' => $project->getLatitude(),
            'longitude' => $project->getLongitude(),
            'roof' => $project->getRoofType(),
            'structure' => $project->getStructureType()
        ];

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager = $this->manager('project');

            $generator = $this->getGenerator();

            /**
             * REPROCESS INVERTERS IF FIELDS IS CHANGED
             * consumption || latitude || longitude
             */
            if($previous['consumption'] != $project->getInfConsumption()
                || $previous['latitude'] != $project->getLatitude()
                || $previous['longitude'] != $project->getLongitude()){

                $generator->reset($project);

                $this->configureProjectFromDefaults($project);
            }

            /**
             * REPROCESS STRUCTURES IF FIELDS IS CHANGED
             * roof || structure
             */
            if($previous['roof'] != $project->getRoofType()
                || $previous['structure'] != $project->getStructureType()){
                $generator->generateStructures($project);
            }

            $manager->save($project);

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
     * @Route("/{id}/chart", name="project_chart")
     * @Method("post")
     */
    public function chartAction(Project $project, Request $request)
    {
        $project->setChart('generation', $request->get('chart'));

        $this->manager('project')->save($project);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/components", name="project_components")
     */
    public function componentsAction(Project $project, Request $request)
    {
        $defaults = $project->getDefaults();

        $form = $this->createForm(GeneratorType::class, $defaults);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $generator = $this->getGenerator();
            $generator->reset($project);

            $project->setDefaults($form->getData());
            $generator->generate($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId(),
                    'power' => $project->getPower()
                ]
            ]);
        }

        return $this->render('project.components', [
            'form' => $form->createView(),
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

            $current = $projectModule->getGroups();

            $groups = $request->request->get('groups');

            foreach ($current as $key => $data){
                if(array_sum($data) != array_sum($groups[$key])){

                    $generator = $this->getGenerator();

                    $projectModule->setGroups($groups);
                    $generator->generateStructures($project);

                    break;
                }
            }

            return $this->json([]);
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

            $this->get('project_generator')->process($project);

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

            $generator = $this->getGenerator();

            if($projectInverter->operationIsChanged()) {

                $project = $projectInverter->getProject();

                $generator->autoSave(false);
                $generator->generateAreasViaProjectInverter($projectInverter);

                $generator
                    ->handleAreas($project)
                    ->generateStringBoxes($project)
                    ->generateVarieties($project);

                $generator->save($project, true);

                $generator->process($project);
            }

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

            $project = $projectArea->getProjectInverter()->getProject();

            $generator = $this->getGenerator();

            $generator
                ->autoSave(false)
                ->generateGroups($project)
                ->generateStructures($project)
                ->generateStringBoxes($project)
                ->handleAreas($project)
                ->generateVarieties($project)
            ;

            $generator->save($project, true);

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
        $this->clearTemplateCache('project.operation_area');

        return $this->render('project.operation_area', [
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
     * @Route("/{id}/steps", name="project_steps")
     * @Method("post")
     */
    public function stepAction(Request $request, Project $project)
    {
        $step = $request->get('step');
        $errors  = [];

        $toFinancial = function() use($project, &$errors){
            if(empty($project->getMetadata())){
                $errors[] = $this->translate('project.error.empty_calculation_metadata');
            }
        };

        $toProposal = function() use($project, &$errors){
            if(empty($project->getAccumulatedCash())){
                $errors[] = $this->translate('financial.error.analysis_not_calculated');
            }
        };

        switch($step) {
            case 'project_financial':
                $toFinancial();
                break;

            case 'project_proposal':
                $toFinancial();
                $toProposal();
                break;
        }

        $url = empty($errors) ? $this->generateUrl($step, ['id' => $project->getId()]) : null ;

        return $this->json([
            'errors' => $errors,
            'url' => $url
        ], empty($errors) ? Response::HTTP_ACCEPTED : Response::HTTP_CONFLICT);
    }

    private function configureProjectFromDefaults(ProjectInterface $project)
    {
        $power = $this->get('power_estimator')->estimate($project->getInfConsumption(), $project->getLatitude(), $project->getLongitude());

        $defaults = $project->getDefaults();

        if(empty($defaults)) {
            $defaults['module'] = 32433;
            $defaults['inverter_maker'] = 60627;
            $defaults['structure_maker'] = 61211;
            $defaults['string_box_maker'] = 61209;
        }

        $defaults['roof_type'] = $project->getRoofType();
        $defaults['power'] = $power;

        /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $generator */
        $generator = $this->get('project_generator');
        $generator->autoSave(false);

        $project->setDefaults($defaults);

        $generator->project($project)->generate();

        $generator->save($project, true);
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

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }
}
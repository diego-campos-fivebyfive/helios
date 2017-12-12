<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectAdditive;
use AppBundle\Entity\Component\ProjectAdditiveInterface;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\ContactInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Project\NasaCatalog;
use AppBundle\Form\Component\ProjectAreaType;
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
        if($member->isOwner() && 'all' != $memberId = $request->get('member')){

            if(!$memberId)
                $memberId = $member->getId();

            $filterMember = $this->manager('customer')->find($memberId);

            if($filterMember instanceof BusinessInterface
                && $filterMember->getAccount()->getId() == $account->getId()){
                $ids = [$filterMember->getId()];
            }
        }

        $qb = $this->manager('project')->createQueryBuilder();

        $qb->join('p.customer', 'c', 'WITH')
            ->join('p.stage', 's')
            ->where($qb->expr()->in('p.member', $ids))
            ->orderBy('p.id', 'desc');

        if(null != $customer = $this->getCustomerReference($request)){
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
        $data = $request->request->get('generator');
        $power = (float) $data['power'];

        if('consumption' == $data['source']){

            $consumption = (float) $data['consumption'];
            $latitude = (float) $data['latitude'];
            $longitude = (float) $data['longitude'];

            $power = $this->get('power_estimator')->estimate($consumption, $latitude, $longitude);
        }

        /** @var \AppBundle\Service\ProjectGenerator\MakerDetector $detector */
        $detector = $this->get('maker_detector');

        $makers = $detector->fromPower($power);

        if(in_array($data['grid_phase_number'], ['Monophasic', 'Biphasic'])){
            $triphasicMakers = $detector->filterNotOnlyTriphasic();
            foreach ($makers as $key => $maker){
                if(!in_array($maker, $triphasicMakers)){
                    unset($makers[$key]);
                }
            }
        }

        $ids = array_map(function(MakerInterface $maker){
            return $maker->getId();
        }, array_values($makers));

        return $this->json([
            'makers' => $ids
        ]);
    }

    /**
     * @Route("/generator/form", name="form_generator")
     */
    public function formGeneratorAction(Request $request)
    {
        $createForm = function(array $data, $action){
            return $this->createForm(GeneratorType::class, $data, [
                'action' => $action
            ]);
        };

        $action = $request->getUri();
        $generator = $this->getGenerator();
        $defaults = $generator->loadDefaults();

        if(null != $id = $request->query->get('project')){
            $project = $this->manager('project')->find($id);
            $defaults = $project->getDefaults();
        }

        $form = $createForm($defaults, $action);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $form = $createForm($form->getData(), $action);
        }

        return $this->render('project.form_generator', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/generator/generate", name="generator_generate")
     */
    public function generateAction(Request $request)
    {
        $project = null;
        $generator = $this->getGenerator();
        $defaults = $generator->loadDefaults();

        if(null != $id = $request->query->get('project')){
            $project = $this->manager('project')->find($id);
            $defaults = $project->getDefaults();
        }

        $form = $this->createForm(GeneratorType::class, $defaults);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$project) $project = $this->manager('project')->create();

            $generator->reset($project);

            $project->setDefaults($defaults);

            $generator->generate($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId(),
                    'power' => $project->getPower()
                ]
            ]);
        }

        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @Route("/create", name="project_create")
     * @Breadcrumb("Novo Projeto")
     */
    public function createAction(Request $request)
    {
        $member = $this->member();

        if($member->getAllowedContacts()->isEmpty()){
            return $this->render('project.alerts', [
                'error' => 'empty_contacts'
            ]);
        }

        $generator = $this->getGenerator();
        $defaults = $generator->loadDefaults(['level'=>$member->getAccount()->getLevel()]);

        $this->checkCustomerReference($defaults, $request);

        $form = $this->createForm(GeneratorType::class, $defaults,[
            'status' => GeneratorType::INIT,
            'member' => $this->member()
        ]);

        $manager = $this->manager('project');

        /** @var Project $project */
        $project = $manager->create();

        $project->setMember($member);

        $form->handleRequest($request);
        $project->setSource(Project::SOURCE_PROJECT);

        if($form->isSubmitted() && $form->isValid()){

            $defaults = $form->getData();
            $generator = $this->getGenerator();

            /** @var \AppBundle\Entity\CustomerInterface $customer */
            $customer = $this->manager('customer')->find($defaults['customer']);

            /** @var \AppBundle\Entity\CategoryInterface $stage */
            $stage = $this->manager('category')->find($defaults['stage']);

            $project
                ->setStage($stage)
                ->setCustomer($customer)
                ->setDefaults($defaults);

            $generator->generate($project);

            $range = null;
            $memorial = $this->container->get('memorial_loader')->load();
            if($memorial) {
                $rangeManager = $this->manager('range');
                $range = $rangeManager->findOneBy([
                    'memorial' => $memorial->getId(),
                    'level' => $member->getAccount()->getLevel()
                ]);
            }
            if(!$memorial || !$range) {
                $defaults['errors'] = ['has_no_memorial_or_range'=>'has_no_memorial_or_range'];
                $project->setDefaults($defaults);
            }

            $errors = self::loadDefaultErrors($project);

            if(count($errors)) {
                return $this->json([
                    'errors' => $errors
                ], Response::HTTP_CONFLICT);
            }

            $project->setNumber($this->incrementAccountIndex('projects'));

            $manager->save($project);

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
        $this->denyAccessUnlessGranted('edit', $project);

        $defaults = $project->getDefaults();

        if(!$project->getMember()){
            $project->setMember($this->member());
        }

        $form = $this->createForm(GeneratorType::class, $defaults, [
            'member' => $project->getMember()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Waiting logic here...
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

        $form = $this->createForm(GeneratorType::class, $defaults, [
            'member' => $project->getMember()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $defaults = $form->getData();
            $generator = $this->getGenerator();
            $generator->reset($project);

            $project->setDefaults($defaults);

            $generator->generate($project);

            $errors = self::loadDefaultErrors($project);

            if(count($errors)) {
                return $this->json([
                    'errors' => $errors
                ], Response::HTTP_CONFLICT);
            }

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

                    if(null != $request->get('precify')){
                        $generator->pricing($project);
                    }

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
            $project = $projectInverter->getProject();

            if($projectInverter->operationIsChanged()) {

                $generator->autoSave(false);
                $generator->generateAreasViaProjectInverter($projectInverter);

                $generator
                    ->handleAreas($project)
                    ->generateStringBoxes($project)
                    ->generateVarieties($project);

                $generator->save($project, true);
            }

            $generator->handleAreas($project)->save($project, true);

            //$generator->process($project);

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
                ->resolveDependencies($project)
                ->handleABBInverters($project) // TODO: Remove this call when changing the ProjectGenerator::$resolveDependencies to true
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
     * @Route("/info_coordinates", name="coordinate_info")
     */
    public function coordinateInfoAction(Request $request)
    {
        $provider = $this->getNasaProvider();

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $accountGlobal = $provider->findOneBy(
            [
                'context' => NasaCatalog::RADIATION_GLOBAL,
                'latitude' => floor($latitude),
                'longitude' => floor($longitude),
                'account' => $this->account()
            ]);

        $infos = $provider->fromCoordinates($latitude, $longitude);

        return $this->render('project.coordinates', [
            'infos' => $infos,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accountGlobal' => $accountGlobal
        ]);
    }

    /**
     * @Route("/{id}/delete", name="project_delete")
     * @Method("delete")
     */
    public function deleteAction(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);

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

            case 'proposal_editor':
                $toFinancial();
                $toProposal();
                break;
        }

        $url = empty($errors) ? $this->generateUrl($step, ['id' => $project->getId()]) : null ;

        return $this->json([
            'errors' => $errors,
            'url' => $url
        ], empty($errors) ? Response::HTTP_ACCEPTED : Response::HTTP_IM_USED);
    }

    /**
     * @Route("/{id}/insurances", name="project_insurances")
     */
    public function insurancesAction(Project $project)
    {
        $synchronizer = $this->get('additive_synchronizer');
        $synchronizer->synchronize($project);

        $insurances = $synchronizer->findBySource($project, Additive::TYPE_INSURANCE);

        return $this->render('admin/projects/insurances_list.html.twig', [
            'insurances' => $insurances,
            'project' => $project
        ]);
    }

    /**
     * @param Project $project
     * @param Additive $additive
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{project}/{additive}/additive/", name="create_project_additive")
     */
    public function createProjectAdditiveAction(Project $project, Additive $additive)
    {
        $manager = $this->manager('project_additive');

        /** @var ProjectAdditiveInterface $projectAdditive */
        $projectAdditive = $manager->create();

        $projectAdditive->setAdditive($additive);
        $projectAdditive->setProject($project);

        $manager->save($projectAdditive);

        return $this->json([]);
    }

    /**
     * @param ProjectAdditive $projectAdditive
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/additive/{projectAdditive}/delete/", name="delete_project_additive")
     */
    public function deleteProjectAdditiveAction(ProjectAdditive $projectAdditive)
    {
        $manager = $this->manager('project_additive');

        $manager->delete($projectAdditive);

        return $this->json([]);
    }

    /**
     * @param array $defaults
     * @param Request $request
     *
     */
    private function checkCustomerReference(array &$defaults, Request $request)
    {
        /** @var BusinessInterface $customer */
        if(null != $customer = $this->getCustomerReference($request)){
            $defaults['customer'] = $customer->getId();
            $defaults['latitude'] = $customer->getLatitude();
            $defaults['longitude'] = $customer->getLongitude();
            $defaults['address'] = $customer->getAddress();
        }
    }

    /*
     * @param Request $request
     * @return BusinessInterface|null
     */
    private function getCustomerReference(Request $request)
    {
        if(null != $token = $request->get('contact')) {

            $customer = $this->manager('customer')->findByToken($token);

            if($customer instanceof ContactInterface) {

                $this->denyAccessUnlessGranted('edit', $customer);

                return $customer;
            }
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

    /**
     * @param Project $project
     * @return array
     */
    private static function loadDefaultErrors(Project $project)
    {
        $errors = [];
        $defaults  = $project->getDefaults();
        if(count($defaults['errors'])){
            if(in_array('exhausted_inverters', $defaults['errors'])) {
                $errors[] = 'Número máximo de inversores excedido para esta configuração.';
            }
            if(in_array('has_no_memorial_or_range', $defaults['errors'])) {
                $errors[] = 'As configurações atuais não permitem a geração de projetos.';
            }
        }

        return $errors;
    }
}

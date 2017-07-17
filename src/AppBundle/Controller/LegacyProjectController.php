<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\KitInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Project\NasaCatalog;
use AppBundle\Entity\Project\Project;
use AppBundle\Entity\Project\ProjectInverter;
use AppBundle\Entity\Project\ProjectModule;
use AppBundle\Form\Project\ProjectInverterType;
use AppBundle\Form\Project\ProjectModuleType;
use AppBundle\Form\Project\ProjectType;
use AppBundle\Form\Settings\DocumentType;
use AppBundle\Service\ProjectHelper;
use AppBundle\Service\ProjectProcessor;
use AppBundle\Service\Security\ProjectAuthorization;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Projects", route={"name"="project_index"})
 * @Route("legacy/project")
 */
class LegacyProjectController extends AbstractController
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
            $ids = $account->getMembers()->map(function (BusinessInterface $m) {
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

        $qb = $this->getProjectManager()->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->getProjectManager()->getClass(), 'p')
            ->join('p.customer', 'c', 'WITH')
            ->join('p.saleStage', 's')
            ->where($qb->expr()->in('p.member', $ids))
            ->orderBy('p.number', 'desc');

        if(null != $customer = $this->getCustomerReferrer($request)){
            $qb->andWhere('p.customer = :customer')->setParameter('customer', $customer);
        }

        if(null != $stage = $request->get('stage', null)){
            $qb->andWhere('s.token = :stage');
            $qb->setParameter('stage', $stage);
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            8
        );

        $saleStages = $account->getCategories(Category::CONTEXT_SALE_STAGE);

        $view = $request->isXmlHttpRequest() ? 'project.index_content' : 'project.index';

        return $this->render($view, [
            'current_stage' => $stage,
            'sale_stages' => $saleStages,
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
        $errors = $this->checkCreateProjectErrors();

        if (!empty($errors)) {
            return $this->render('alerts', [
                'errors' => $errors
            ]);
        }

        $member = $this->member();

        $manager = $this->getProjectManager();

        $project = $manager->create($member);

        $this->handleCustomerReferrer($project, $request);

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project->setMetadata('number', $this->incrementAccountIndex('project_index'));
            $manager->save($project);

            $account = $member->getAccount();
            if($account->isFreeAccount()){
                $account->incrementProjectsCount();
                $this->getCustomerManager()->save($account);
            }

            return $this->redirectToRoute('project_update', [
                'token' => $project->getToken()
            ]);
        }

        return $this->render('project._form', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Breadcrumb("Projetos - N&deg; {project.number}")
     * @Route("/{token}/update", name="project_update")
     */
    public function updateAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        if ($project->isDone()) {
            return $this->render('project.done', [
                'project' => $project,
                'stage' => 'scaling'
            ]);
        }

        $previousKit = $project->getKit();
        $this->synchronizeProject($project, $previousKit);

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->synchronizeProject($project, $previousKit);

            $this->getProjectManager()->save($project);

            return $this->jsonResponse([
                'updated' => $project->getToken()
            ]);
        }

        return $this->render('project._form', [
            'form' => $form->createView(),
            'project' => $project,
            'ProjectModule' => ProjectModule::class
        ]);
    }

    /**
     * @Route("/{token}/forms/inverters", name="project_inverters_forms")
     */
    public function getInvertersFormsAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        //$this->synchronizeProject($project);

        $forms = [];
        foreach ($project->getInverters() as $projectInverter) {

            $form = $this->createForm(ProjectInverterType::class, $projectInverter);

            $forms[] = $this->renderView('project.form_inverter', [
                'form' => $form->createView(),
                'project_inverter' => $projectInverter
            ]);
        }

        return $this->jsonResponse([
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/{id}/modules/forms", name="project_modules_forms")
     */
    public function getModulesFormsAction(ProjectInverter $projectInverter)
    {
        $forms = [];
        foreach ($projectInverter->getModules() as $projectModule) {

            $form = $this->createForm(ProjectModuleType::class, $projectModule);

            $forms[] = $this->renderView('project.form_module', [
                'project_module' => $projectModule,
                'form' => $form->createView()
            ]);
        }

        return $this->jsonResponse([
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/inverter/{id}/update", name="project_inverter_update")
     */
    public function updateInverterAction(Request $request, ProjectInverter $projectInverter)
    {
        $form = $this->createForm(ProjectInverterType::class, $projectInverter);

        $previousOperation = $projectInverter->getOperation();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if ($previousOperation != $projectInverter->getOperation()) {

                foreach ($projectInverter->getModules() as $projectModule) {
                    $em->remove($projectModule);
                }

                // Create new modules
                for ($i = 0; $i < $projectInverter->getOperation()->count(); $i++) {
                    $projectModule = new ProjectModule();
                    $projectModule->setInverter($projectInverter);

                    $em->persist($projectModule);
                }
            }

            $em->flush();

            return $this->jsonResponse();
        }

        return $this->jsonResponse([
            'id' => $projectInverter->getId()
        ]);
    }

    /**
     * @Route("/module/{id}/update", name="project_module_update")
     */
    public function updateModuleAction(Request $request, ProjectModule $projectModule)
    {
        $this->checkAccess($projectModule->getInverter()->getProject());

        $form = $this->createForm(ProjectModuleType::class, $projectModule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            /** @var ProjectHelper $processor */
            $helper = $this->get('app.project_helper');
            $helper->debugArea($projectModule);
        }

        return $this->json([
            'module' => [
                'power' => $projectModule->getPower(),
                'area' => $projectModule->getTotalArea()
            ]
        ]);
    }


    /**
     * @Route("/{token}/info", name="project_info")
     */
    public function infoAction(Project $project)
    {
        $this->checkAccess($project);

        return $this->render('project.info', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/operations", name="project_check_operations")
     */
    public function checkOperationsAction(ProjectInverter $projectInverter)
    {
        $this->checkAccess($projectInverter->getProject());

        return $this->jsonResponse(
            $projectInverter->getMetadataOperation()
        );
    }

    /**
     * @Route("/{id}/inverter-operation", name="legacy_project_inverter_operation")
     */
    public function inverterOperationAction(ProjectInverter $projectInverter)
    {
        $this->checkAccess($projectInverter->getProject());

        return $this->render('project.inverter_operation', [
            'project_inverter' => $projectInverter
        ]);
    }

    /**
     * @Route("/module/{id}/area-operation", name="area_operation")
     */
    public function areaOperationAction(ProjectModule $projectModule)
    {
        $this->checkAccess($projectModule->getInverter()->getProject());

        if ($projectModule->isComputable()) {

            /** @var ProjectHelper $processor */
            $helper = $this->get('app.project_helper');
            $helper->debugArea($projectModule);

            $data = $projectModule->getMetadataOperation();

            $this->clearTemplateCache('project.area_operation');

            return $this->render('project.area_operation', [
                'data' => $data
            ]);
        }

        return $this->render('project.area_errors', [
            'project_module' => $projectModule
        ]);
    }

    /**
     * @Route("/{token}/process", name="legacy_project_process")
     */
    public function processAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        if ($project->isComputable()) {

            /** @var ProjectHelper $helper */
            $helper = $this->get('app.project_helper');
            $helper->calculateProject($project);

            //dump($project->getMetadata('areas')); die;

            if (!$project->hasMetadata('areas')) {
                return $this->jsonResponse(['error' => 'Unprocessed Project'], Response::HTTP_NO_CONTENT);
            }

            $general = [
                'kwh_year' => 0,
                'kwh_month' => 0,
                'kwh_kwp_year' => 0,
                'kwh_kwp_month' => 0
            ];
            foreach ($project->getMetadata('areas') as $area) {
                $general['kwh_year'] += $area['kwh_year'];
                $general['kwh_month'] += $area['kwh_month'];
                $general['kwh_kwp_year'] += $area['kwh_kwp_year'];
                $general['kwh_kwp_month'] += $area['kwh_kwp_month'];
            }

            return $this->jsonResponse([
                'data' => $project->getMonthlyProduction(),
                'info' => $this->renderView('project.processed', [
                    'general' => $general,
                    'project' => $project
                ]),
                'warnings' => $this->renderView('project.warnings', [
                    'project' => $project
                ])
            ]);
        }

        return $this->render('project.errors', [
            'errors' => $project->getErrors()
        ]);
    }

    /**
     * @Route("/info_coordinates", name="coordinate_info")
     * @Method("post")
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
                'account' => $this->getCurrentAccount()
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
     * @Route("/estimate-power", name="project_estimate_power")
     * @Method("post")
     */
    public function estimatePowerAction(Request $request)
    {
        $data = $request->request->get('data');

        $latitude = (float)$data['latitude'];
        $longitude = (float)$data['longitude'];
        $consumption = (float)$data['consumption'];

        $error = 'Coordenadas indefinidas';

        if ($latitude && $longitude && $consumption) {

            /** @var ProjectHelper $processor */
            $helper = $this->get('app.project_helper');

            try {

                $power = $helper->estimatePower($latitude, $longitude, $consumption);

                return $this->jsonResponse([
                    'power' => round($power, 2)
                ]);

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return $this->jsonResponse([
            'error' => $error
        ]);
    }

    /**
     * @Route("/form")
     */
    public function formAction()
    {
        return $this->render('project.form');
    }

    /**
     * @Route("/{token}/check_step", name="project_check_step_legacy")
     */
    public function checkStepAction(Request $request, Project $project)
    {
        $step = $request->request->get('step');
        $error = null;
        $status = Response::HTTP_ACCEPTED;

        switch ($step) {
            case 'financial':
                if (!$project->isAnalysable()) {
                    $errors = $project->getAnalysisErrors();
                    $status = Response::HTTP_CONFLICT;

                    if($errors[0] != 'incompatible_number_of_modules'){
                        $error = $this->translate(sprintf('project.error.%s', $errors[0]));
                    }else{
                        $error = 'O KIT Selecionado está configurado como: <br> '
                        .'<strong> Desejo informar apenas o custo total dos equipamentos </strong> <br>'
                        .'Por esse motivo, a quantidade de módulos configurados deve coincidir com o número de módulos do kit.';
                    }
                }
                break;

            case 'proposal':
                $financial = $this->getProjectFinancial($project);
                if (!$financial->allowProposal()) {
                    $errors = $financial->getProposalErrors();
                    $status = Response::HTTP_CONFLICT;
                    $error = $this->translate(sprintf('financial.error.%s', $errors[0]));
                }
                break;

            default:
                $error = 'invalid_step_argument';
                $status = Response::HTTP_CONFLICT;
                break;
        }

        return $this->jsonResponse(['error' => $error], $status);
    }

    /**
     * @Breadcrumb("Proposta Gerada")
     * @Route("/{token}/done", name="project_done")
     */
    public function doneAction(Project $project)
    {
        //$media = $this->get('sonata.media.manager.media')->find(118);


        /** @var \Sonata\MediaBundle\Provider\FileProvider $providerFile */
        $providerFile = $this->get('sonata.media.provider.file');

        $this->dd($providerFile->getFilesystem()->getAdapter());

        $this->dd($this->get('sonata.media.provider.file'));

        return $this->render('project.done', [
            'project' => $project,
            //'media' => $media
        ]);
    }

    /**
     * @Route("/{token}/copy", name="project_copy")
     * @Method("post")
     */
    public function copyAction(Project $project)
    {
        $errors = $this->checkCopyProjectErrors($project);

        if (!empty($errors)) {
            
            //$keys = array_keys($errors);
            //$error = is_string($keys[0]) ? $keys[0] : $errors[0] ;

            $error = $errors[0];
            if(in_array('projects_quota_is_reached', $errors)){
                $error = $this->translate('projects_quota_is_reached');
            }
            
            return $this->jsonResponse([
                'error' => $error //$this->translate($error)
            ], Response::HTTP_CONFLICT);
        }

        /** @var Project $projectCopy */
        $projectCopy = $this->get('app.project_helper')->copyProject($project);
        
        $member = $this->member();
        $account = $member->getAccount();

        // Change project owner by current member
        $projectCopy->setMember($member);
        $this->getProjectManager()->save($projectCopy);

        // Increment projects count attribute
        if($account->isFreeAccount()){
            $account->incrementProjectsCount();
            $this->getCustomerManager()->save($account);
        }        
        
        return $this->jsonResponse([
            'url' => $this->generateUrl('project_update', ['token' => $projectCopy->getToken()])
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/{token}/change", name="project_change")
     * @Method("post")
     */
    public function changeAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        $provider = $request;
        $target = $provider->get('target');

        switch ($target) {
            case 'stage':

                $token = $provider->get($target);

                $stage = $this->getCategoryManager()->findOneBy([
                    'account' => $this->getCurrentAccount(),
                    'context' => Category::CONTEXT_SALE_STAGE,
                    'token' => $token
                ]);

                if ($stage instanceof Category) {
                    $project->setSaleStage($stage);

                    $this->getProjectManager()->save($project);
                }

                break;
        }

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/{token}/delete", name="project_delete_legacy")
     * @Method("delete")
     */
    public function deleteAction(Project $project)
    {
        $this->checkAccess($project);

        $em = $this->getDoctrine()->getManager();
        
        if (null != $financial = $project->getFinancial()) {
            $proposal = $financial->getProposal();
            $em->remove($financial);
            if ($proposal) {
                $em->remove($proposal);
            }
        }

        if (null != $filename = $project->getMetadata('filename')) {
            $this->getFileExplorer()->delete($project);

        }

        foreach ($project->getInverters() as $projectInverter) {
            $project->removeInverter($projectInverter);
            $em->remove($projectInverter);
        }
        $em->remove($project);
        $em->flush();

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @param Project $project
     */
    private function synchronizeProject(Project $project, Kit $previousKit = null)
    {
        if (!$project->assertKitDistribution($previousKit)) {

            $em = $this->getDoctrine()->getManager();
            foreach ($project->getInverters() as $projectInverter) {
                $em->remove($projectInverter);
            }
            $em->flush();

            $this->generateInverters($project);

            $em->refresh($project);
        }
    }

    /**
     * @param Project $project
     */
    private function generateInverters(Project &$project)
    {
        $kitInverters = $project->getKit()->getInverters();

        $em = $this->getDoctrine()->getManager();
        foreach ($kitInverters as $kitInverter) {
            for ($i = 0; $i < $kitInverter->getQuantity(); $i++) {

                $projectInverter = new ProjectInverter();
                $projectInverter
                    ->setProject($project)
                    ->setInverter($kitInverter);

                $em->persist($projectInverter);
                $em->flush();
            }
        }
    }

    /**
     * Check all authorizations levels
     * @param $target
     */
    private function checkAccess($target)
    {
        $this->get('app.project_authorization')->isAuthorized($target);
    }

    /**
     * @param Project $project
     * @return ProjectFinancialInterface
     * @throws \Exception
     */
    private function getProjectFinancial(Project $project)
    {
        return $this->getFinancialManager()->fromProject($project);
    }

    /**
     * @return ProjectFinancialManager|object
     */
    private function getFinancialManager()
    {
        return $financialManager = $this->get('app.project_financial');
    }

    /**
     * @param Project $project
     * @return array
     */
    private function checkCopyProjectErrors(Project $project)
    {
        $errors = $this->checkCreateProjectErrors();

        $snapshot = $project->getSnapshot();

        if (empty($snapshot) || !array_key_exists('kit', $snapshot)) {

            $errors[] = 'project.error.copy_not_kit_identity';
        } else {

            $kitId = $snapshot['kit']['id'];
            $kit = $this->getKitManager()->find($kitId);

            if (!$kit instanceof KitInterface) {
                $errors[] = 'project.error.copy_kit_not_found';
            }
        }

        return $errors;
    }

    /**
     * @return array
     */
    private function checkCreateProjectErrors()
    {
        $errors = [];
        $account = $this->account();

        /*if($account->projectsQuotaIsReached()){
            $errors[] = 'projects_quota_is_reached';
        }*/

        if ($account->getKits()->isEmpty()) {
            $errors[] = 'project.error.no_registered_kit';
        } else {
            $applicable = $account->getKits()->filter(function (KitInterface $kit) {
                return $kit->isApplicable();
            });
            if ($applicable->isEmpty()) {
                $errors[] = 'No applicable kit found';
            }
        }

        return $errors;
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\CategoryManager|object
     */
    /*private function getCategoryManager()
    {
        return $this->get('sonata.classification.manager.category');
    }*/

    /**
     * @return \AppBundle\Entity\DocumentManager|object
     */
    private function getDocumentManager()
    {
        return $this->get('app.document_manager');
    }

    /**
     * @param Project $project
     * @param Request $request
     */
    private function handleCustomerReferrer(Project &$project, Request $request)
    {
        if(null != $customer = $this->getCustomerReferrer($request)){
            $project->setCustomer($customer);
        }
    }


    /**
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
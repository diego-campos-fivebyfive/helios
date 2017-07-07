<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Form\Component\ProjectAreaType;
use AppBundle\Form\Component\ProjectType;
use AppBundle\Form\Project\ProjectInverterType;
use AppBundle\Service\ProjectHelper;
use AppBundle\Service\ProjectProcessor;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ProjectController
 * @Route("project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/configure")
     */
    public function configureAction()
    {
        $project = $this->debugProject();

        /** @var ProjectProcessor $processor */
        //$processor = $this->container->get('app.project_processor');
        //$metadata = $processor->process($project);

        //$member  = $this->member();
        //$manager = $this->manager('project');

        //$project = $manager->create();
        //$project->setMember($member);

        $form = $this->createForm(ProjectType::class, $project);

        //dump($project); die;

        return $this->render('project.configure', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/update", name="project_update")
     */
    public function updateAction(Request $request, Project $project)
    {
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            dump($project); die;
        }

        return $this->render('project.form', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/process", name="project_process")
     */
    public function processAction(Request $request, Project $project)
    {
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

            $this->resetProjectAreas($projectInverter);

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
    public function inverterOperationAction(ProjectInverter $projectInverter)
    {
        return $this->render('project.inverter_operation', [
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
    public function areaOperationAction(ProjectArea $projectArea)
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
     * @return Project|null|object
     * TODO Remove before send homolog
     */
    private function debugProject()
    {
        $manager = $this->manager('project');

        $project = $manager->find(103);

        return $project;

        /** @var Project $project */
        $project = $manager->create();
        $project
            ->setNumber(rand(1, 500))
            ->setMember($this->member())
            ->setIdentifier('Abc-125')
            ->setAddress('This is a address')
            ->setLatitude(-25.4237919)
            ->setLongitude(-49.216131)
        ;

        /** @var \AppBundle\Entity\CustomerInterface $customer */
        $customer = $this->manager('customer')->find(34);
        /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
        $inverter = $this->manager('inverter')->find(5861);
        /** @var \AppBundle\Entity\Component\ModuleInterface $module */
        $module = $this->manager('module')->find(32432);

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        $projectModule = new ProjectModule();
        $projectModule
            ->setProject($project)
            ->setModule($module)
            ->setQuantity(24)
        ;

        $project->setCustomer($customer);

        /*for($i = 1; $i < 3; $i++){
            $projectArea = new ProjectArea();
            $projectArea
                ->setProjectInverter($projectInverter)
                ->setProjectModule($projectModule)
                ->setInclination(20)
                ->setStringNumber(1)
                ->setModuleString(4)
            ;
        }*/

        $manager->save($project);

        dump($project); die;
    }

    /**
     * @param ProjectInverter $projectInverter
     */
    private function resetProjectAreas(ProjectInverter $projectInverter)
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
}

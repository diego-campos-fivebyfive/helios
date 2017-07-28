<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Form\Component\ProjectInverterType;
use AppBundle\Form\Component\ProjectModuleType;
use AppBundle\Form\Component\ProjectStringBoxType;
use AppBundle\Form\Component\ProjectStructureType;
use AppBundle\Form\Generator\GeneratorType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("project/generator")
 */
class ProjectGeneratorController extends AbstractController
{
    /**
     * @Route("/", name="project_generator")
     */
    public function indexAction(Request $request)
    {
        /*$form = $this->createForm(GeneratorType::class);

        $form->handleRequest($request);

        $project = $this->manager('project')->create();

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $generator = $this->getGenerator();
            $generator->autoSave(false);

            $project
                ->setRoofType($data['roof'])
                ->setStructureType($data['structure']);

            $project = $generator
                ->project($project)
                ->power((float)$data['power'])
                ->module($data['module'], $data['position'])
                ->maker($data['maker'])
                ->generate();
        }*/

        $form = $this->createForm(GeneratorType::class);

        return $this->render('generator.index', [
            'form' => $form->createView()
            //'form' => $form->createView(),
            //'project' => $project
        ]);
    }

    /**
     * @Route("/inverters/{id}/update")
     */
    public function updateInverterAction(ProjectInverter $projectInverter, Request $request)
    {
        $form = $this->createForm(ProjectInverterType::class, $projectInverter);

        return $this->render('generator.form_inverter', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/inverters/create", name="project_inverter_create")
     */
    public function updateCreateAction(Project $project, Request $request)
    {
        $manager = $this->manager('project_inverter');
        $projectInverter = $manager->create();

        $form = $this->createForm(ProjectInverterType::class, $projectInverter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $projectInverter->setProject($project);

            $manager->save($projectInverter);

            return $this->json([

            ]);
        }

        return $this->render('generator.form_inverter', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modules/{id}/update")
     */
    public function updateModuleAction(ProjectModule $projectModule, Request $request)
    {
        $form = $this->createForm(ProjectModuleType::class, $projectModule);

        return $this->render('generator.form_module', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/string-boxes/{id}/update")
     */
    public function updateStringBoxAction(ProjectStringBox $projectStringBox, Request $request)
    {
        $form = $this->createForm(ProjectStringBoxType::class, $projectStringBox);

        return $this->render('generator.form_string_box', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/structures/{id}/update")
     */
    public function updateStructureAction(ProjectStructure $projectStructure, Request $request)
    {
        $form = $this->createForm(ProjectStructureType::class, $projectStructure);

        return $this->render('generator.form_structure', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/varieties/{id}/update")
     */
    public function updateVarietyAction(ProjectVariety $projectVariety, Request $request)
    {
        $form = $this->createForm(ProjectStructureType::class, $projectVariety);

        return $this->render('generator.form_variety', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="project_generator_update")
     */
    public function updateAction(Request $request, Project $project)
    {
        if($request->isMethod('post')){

            return $this->json([
                'data' => $request->request->all()
            ]);
        }

        return $this->render('generator.update', [
            'project' =>$project
        ]);
    }

    /**
     * @Route("/form", name="project_generator_form")
     */
    public function formsAction(Request $request)
    {
        $form = $this->createForm(GeneratorType::class);

        return $this->render('generator.form', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/generate", name="project_generator_generate")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(GeneratorType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $power = (float)$data['power'];
            $module = $data['module'];
            $maker = $data['maker'];
            $roof = $data['roof'];
            $position = $data['position'];
            $structure = $data['structure'];

            $generator = $this->getGenerator();
            $generator->autoSave(false);

            $manager = $this->manager('project');

            /** @var Project $project */
            $project = $manager->create();

            $project
                ->setRoofType($roof)
                ->setStructureType($structure);

            $project = $generator
                ->project($project)
                ->power($power)
                ->module($module, $position)
                ->maker($maker)
                ->generate();

            $manager->save($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId()
                ]
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}/components", name="project_generator_components")
     */
    public function componentsAction(Request $request, Project $project)
    {
        return $this->render('generator.components', [
            'project' => $project
        ]);
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }
}
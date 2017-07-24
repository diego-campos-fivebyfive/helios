<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Form\Component\GeneratorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
        $form = $this->createForm(GeneratorType::class);

        $form->handleRequest($request);

        /** @var Project $project */
        $project = $this->manager('project')->create();

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $power = (float) $data['power'];
            $module = $data['module'];
            $maker = $data['maker'];
            $roof = $data['roof'];
            $position = $data['position'];
            $structure = $data['structure'];

            $generator = $this->getGenerator();
            $generator->autoSave(false);

            $project
                ->setRoofType($roof)
                ->setStructureType($structure);

            $project = $generator
                ->project($project)
                ->power($power)
                ->module($module, $position)
                ->maker($maker)
                ->generate();
        }

        return $this->render('generator.index', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/forms", name="project_generator_forms")
     */
    public function formsAction(Request $request)
    {
        $number = $request->get('number');

        $forms = [];
        for ($i=1; $i <= $number; $i++){
            $forms[] = $this->createForm(GeneratorType::class)->createView();
        }

        return $this->render('generator.forms', [
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/generate", name="project_generator_generate")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(GeneratorType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $power = (float) $data['power'];
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
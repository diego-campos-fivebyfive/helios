<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Form\Component\GeneratorType;
use AppBundle\Service\ProjectGenerator\ShippingRuler;
use AppBundle\Form\Financial\ShippingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/generator")
 */
class GeneratorController extends AbstractController
{
    /**
     * @var \AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private $generator;

    /**
     * @Route("/", name="generator")
     */
    public function indexAction(Request $request)
    {
        $action = $request->getUri();
        $option = $request->request->get('_option');
        $project = $this->resolveProject($request);

        $generator = $this->getGenerator();

        $defaults = $generator->loadDefaults($project->getDefaults());

        $form = $this->createForm(GeneratorType::class, $defaults, [
            'action' => $action
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            if('save' != $option){

                $generator->reset($project);
                $form = $this->createForm(GeneratorType::class, $form->getData(), [
                    'action' => $action
                ]);

            }else{

                $project->setDefaults($form->getData());

                $generator->reset($project);
                $generator->generate($project);

                $errors = self::loadDefaultErrors($project);

                if(count($errors)) {
                    return $this->json([
                        'error' => $errors[0]
                    ], Response::HTTP_CONFLICT);
                }

                //$project->setNumber($this->incrementAccountIndex('projects'));
                //$manager->save($project);

                return $this->json([
                    'project' => [
                        'id' => $project->getId(),
                        'power' => $project->getPower()
                    ]
                ]);
            }
        }

        return $this->render('generator.form', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/shipping", name="financial_shipping")
     */
    public function shippingAction(Request $request, Project $project)
    {
        $rule = $project->getShippingRules();
        $form = $this->createForm(ShippingType::class, $rule);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $rule = $form->getData();

            ShippingType::normalize($rule);

            $rule['price'] = $project->getCostPriceComponents();
            $rule['power'] = $project->getPower();

            ShippingRuler::apply($rule);

            $project->setShippingRules($rule);

            $this->manager('project')->save($project);

            return $this->json([
                'shipping' => $project->getShipping()
            ]);
        }

        return $this->render('project.financial_shipping', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Project
     */
    private function resolveProject(Request $request)
    {
        $manager = $this->manager('project');

        $project = $manager->create();
        if(0 != $id = $request->query->getInt('project', 0)){
            $project = $manager->find($id);
        }

        if(!$project->getMember()){
            $project->setMember($this->member());
        }

        return $project;
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        if(!$this->generator)
            $this->generator = $this->get('project_generator');

        return $this->generator;
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
        }

        return $errors;
    }
}
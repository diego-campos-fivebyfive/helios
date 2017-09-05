<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ProjectExtra;
use AppBundle\Entity\Component\ProjectTax;
use AppBundle\Entity\Component\Project;
use AppBundle\Form\Component\ProjectExtraType;
use AppBundle\Form\Financial\FinancialType;
use AppBundle\Form\Financial\ShippingType;
use AppBundle\Form\Financial\TaxType;
use AppBundle\Service\Pricing\Insurance;
use AppBundle\Service\ProjectGenerator\ShippingRuler;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Projects", route={"name"="project_index"})
 * @Route("/project/financial")
 */
class ProjectFinancialController extends AbstractController
{
    /**
     * @Breadcrumb("Análise Financeira - N&deg; {project.number}")
     * @Route("/{id}", name="project_financial")
     */
    public function configAction(Project $project)
    {
        $form = $this->createForm(FinancialType::class, $project);

        $formTax = $this->createForm(TaxType::class, new ProjectTax());

        return $this->render('project.financial', [
            'project' => $project,
            'form' => $form->createView(),
            'form_tax' => $formTax->createView()
        ]);
    }

    /**
     * @Route("/{id}/components", name="financial_components")
     */
    public function componentsAction(Project $project, Request $request)
    {
        $defaults = $project->getDefaults();

        if(null != $isPromotional = $request->query->get('is_promotional')){
            $defaults['is_promotional'] = (bool) $isPromotional;
            $project->setDefaults($defaults);
        }

        $generator = $this->getGenerator();

        $generator->pricing($project);

        return $this->render('project.financial_components', [
            'defaults' => $defaults,
            'project' => $project
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
     * @Route("/{id}/final_prices", name="financial_final_prices")
     */
    public function getFinalPricesAction(Project $project)
    {
        return $this->render('project.financial_prices', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/calculate", name="financial_calculate")
     */
    public function calculateAction(Request $request, Project $project)
    {
        $response = ['error' => 'Unprocessed metadata'];

        if ($request->isMethod('post')) {

            $form = $this->createForm(FinancialType::class, $project);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                FinancialAnalyzer::analyze($project);

                $this->manager('project')->save($project);

            } else {
                $response['error'] = 'Invalid form data';
            }
        }

        $this->clearTemplateCache('project.financial_info');

        if ($project->getAccumulatedCash()) {
            $response = [
                'data' => $project->getAccumulatedCash(),
                'info' => $this->renderView('project.financial_info', [ 'project' => $project ])
            ];
        }

        if($request->isXmlHttpRequest()) {
            return $this->json($response);
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("/{id}/extras", name="financial_extras")
     */
    public function getExtrasAction(Project $project)
    {
        return $this->render('project.extras', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{id}/extras/create/{type}", name="financial_extras_create")
     */
    public function createExtraAction(Request $request, Project $project, $type)
    {
        $projectExtra = new ProjectExtra();
        $projectExtra->setProject($project);

        $form = $this->createForm(ProjectExtraType::class, $projectExtra, [
            'type' => $type,
            'action' => $request->getUri()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($projectExtra->getExtra()) {
                $this->manager('project_extra')->save($projectExtra);
                $this->getGenerator()->pricing($project);
            }

            return $this->json([]);
        }

        return $this->render('project.form_extra', [
            'form' => $form->createView(),
            'type' => $type,
            'project' => $project,
            'projectExtra' => $projectExtra
        ]);
    }

    /**
     * @Route("/extras/{id}/update", name="financial_extras_update")
     */
    public function updateExtraAction(Request $request, ProjectExtra $projectExtra)
    {
        $form = $this->createForm(ProjectExtraType::class, $projectExtra, [
            'type' => $projectExtra->getExtra()->getType(),
            'action' => $request->getUri()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->getGenerator()->pricing($projectExtra->getProject());

            return $this->json([]);
        }

        return $this->render('project.form_extra', [
            'form' => $form->createView(),
            'type' => $projectExtra->getExtra()->getType(),
            'project' => $projectExtra->getProject(),
            'projectExtra' => $projectExtra
        ]);
    }

    /**
     * @Route("/extras/{id}/delete", name="financial_extras_delete")
     * @Method("delete")
     */
    public function deleteExtraAction(ProjectExtra $projectExtra)
    {
        $this->manager('project_extra')->delete($projectExtra);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/insure", name="financial_insure")
     * @Method("post")
     */
    public function insureAction(Project $project, Request $request)
    {
        Insurance::apply($project, (bool) $request->get('insure'));

        $this->manager('project')->save($project);

        return $this->json([
            'project' => [
                'id' => $project->getId(),
                'insurance' => $project->getInsurance()
            ]
        ]);
    }

    /**
     * @Route("/{id}/taxes", name="financial_taxes")
     */
    public function taxesAction(Project $project)
    {
        return $this->json([
            'content' => $this->renderView('project.financial_taxes', ['project' => $project])
        ]);
    }

    /**
     * @Route("/{id}/taxes/create", name="financial_tax_create")
     */
    public function createTaxAction(Request $request, Project $project)
    {
        $tax = new ProjectTax();
        $tax->setProject($project);

        return $this->handleTaxApplication($request, $tax);
    }

    /**
     * @Route("/tax/{id}/update", name="financial_tax_update")
     */
    public function updateTaxAction(Request $request, ProjectTax $tax)
    {
        return $this->handleTaxApplication($request, $tax);
    }

    /**
     * @Route("/tax/{id}/delete", name="financial_tax_delete")
     * @Method("delete")
     */
    public function deleteTaxAction(ProjectTax $tax)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($tax);
        $manager->flush();

        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param ProjectTax $tax
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function handleTaxApplication(Request $request, ProjectTax $tax)
    {
        $project = $tax->getProject();

        /** @var ProjectTax $backupTax */
        $backupTax = $tax->getId() ?  clone $tax : null ;

        $form = $this->createForm(TaxType::class, $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($tax);
            $manager->flush();

            $status = Response::HTTP_ACCEPTED;
            $data = [];

            //if ($financial->getFinalPrice() <= 0.01) {
            if ($project->getSalePrice() <= 0.01) {

                $status = Response::HTTP_CONFLICT;
                $data = [
                    'error' => $this->get('translator')->trans('financial.error.negative_selling_price'),
                    'total' => $project->getSalePrice(),
                    'tax_value' => $tax->getValue()
                ];

                if(!$backupTax) {
                    $manager->remove($tax);
                }else{

                    $tax->setName($backupTax->getName())
                        ->setTarget($backupTax->getTarget())
                        ->setOperation($backupTax->getOperation())
                        ->setValue($backupTax->getValue())
                    ;
                }

                $manager->flush();
            }

            return $this->json($data, $status);
        }

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }
}

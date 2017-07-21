<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ProjectExtra;
use AppBundle\Entity\Component\ProjectTax;
use AppBundle\Entity\Financial\ProjectFinancial;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Financial\Tax;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Form\Component\ProjectExtraType;
use AppBundle\Form\Financial\FinancialType;
use AppBundle\Form\Financial\TaxType;
use AppBundle\Model\KitPricing;
use AppBundle\Service\Component\ProjectPrecifier;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use AppBundle\Util\ProjectPricing\SalePrice;
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
    public function configAction(Request $request, Project $project)
    {
        //$formExtra = $this->addExtraAction($request, $project, 0);
        //dump($formExtra); die;

        /**
         * TODO: Calculate cost prices
         */
        $precifier = new ProjectPrecifier($this->manager('project'));
        $precifier->priceCost($project);

        /**
         * TODO: Calculate sale prices
         */
        /** @var \AppBundle\Entity\Component\PricingManager $pricingManager */
        $pricingManager = $this->get('app.kit_pricing_manager');
        $margins = $pricingManager->findAll();
        $percentEquipments = 0;
        $percentServices = 0;
        /** @var \AppBundle\Model\KitPricing $margin */
        foreach ($margins as $margin){
            switch ($margin->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $percentEquipments += $margin->percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $percentServices += $margin->percent;
                    break;
                default:
                    $percentServices += $margin->percent;
                    $percentEquipments += $margin->percent;
                    break;
            }
        }

        //dump($percentEquipments); die;

        SalePrice::calculate($project, $percentEquipments, $percentServices);

        $this->manager('project')->save($project);

        $form = $this->createForm(FinancialType::class, $project);

        $formTax = $this->createForm(TaxType::class, new ProjectTax());

        return $this->render('project.financial', [
            'project' => $project,
            //'financial' => $financial,
            'form' => $form->createView(),
            'form_tax' => $formTax->createView()
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

            $this->manager('project')->save($project);

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

            $this->manager('project')->save($projectExtra->getProject());

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
    public function deleteExtraAction(Request $request, ProjectExtra $projectExtra)
    {
        $this->manager('project_extra')->delete($projectExtra);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/taxes", name="financial_taxes")
     */
    public function taxesAction(Request $request, Project $project)
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
    public function deleteTaxAction(Request $request, ProjectTax $tax)
    {
        // For errors
        /* return $this->jsonResponse([
             'error' => 'This is a custom error'
         ], Response::HTTP_OK);*/

        $manager = $this->getDoctrine()->getManager();

        $manager->remove($tax);
        $manager->flush();

        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @param ProjectTax $tax
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function handleTaxApplication(Request $request, ProjectTax $tax)
    {
        //$financial = $tax->getFinancial();

        $project = $tax->getProject();

        /** @var Tax $backupTax */
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
     * @param Project $project
     * @return ProjectFinancialInterface
     * @throws \Exception
     */
    private function getProjectFinancial(Project $project)
    {
        return $this->getFinancialManager()->fromProject($project);
    }

    /**
     * @return ProjectFinancialManager
     */
    private function getFinancialManager()
    {
        return $financialManager = $this->get('app.project_financial');
    }
}

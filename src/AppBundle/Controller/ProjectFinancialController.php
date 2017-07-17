<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ProjectTax;
use AppBundle\Entity\Financial\ProjectFinancial;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Financial\Tax;
use AppBundle\Entity\Component\Project;
use AppBundle\Form\Financial\FinancialType;
use AppBundle\Form\Financial\TaxType;
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
    public function configAction(Project $project)
    {
        /*$tax = new ProjectTax();
        $tax
            ->setName('The Tax')
            ->setOperation(ProjectTax::OPERATION_ADDITION)
            ->setTarget(ProjectTax::TARGET_EQUIPMENTS)
            ->setType(ProjectTax::TYPE_ABSOLUTE)
            ->setValue(1000)
            ->setProject($project)
        ;

        dump('Equipments: ' . $project->getSalePriceEquipments());
        dump('Services: ' . $project->getSalePriceServices());
        dump('Tax: ' . $tax->getAmount());
        dump('Sale: ' . $project->getSalePrice());
        die;*/


        /*if($project->isDone()){
            return $this->render('project.done', [
                'project' => $project,
                'stage' => 'financial'
            ]);
        }
        
        if(!$project->isAnalysable()) {
            return $this->redirectToRoute('project_update',['token' => $project->getToken()]);
        }*/
        
        //$financial = $this->getProjectFinancial($project);

        // TODO : REVIEW THIS PROCESS
        /*SalePrice::calculate($project, 10, 10);
        $this->manager('project')->save($project);
        dump($project->getSalePrice()); die;*/

        $form = $this->createForm(FinancialType::class, $project);

        //dump($form); die;
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

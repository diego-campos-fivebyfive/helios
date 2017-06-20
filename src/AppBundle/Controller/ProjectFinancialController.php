<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Precifier;
use AppBundle\Entity\Financial\ProjectFinancial;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Financial\Tax;
use AppBundle\Entity\Project\Project;
use AppBundle\Form\Financial\FinancialType;
use AppBundle\Form\Financial\TaxType;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Projects", route={"name"="project_index"})
 * @Route("/financial")
 */
class ProjectFinancialController extends AbstractController
{
    /**
     * @Breadcrumb("Análise Financeira - N&deg; {project.number}")
     * @Route("/{token}", name="project_financial")
     */
    public function configAction(Project $project)
    {
        if($project->isDone()){
            return $this->render('project.done', [
                'project' => $project,
                'stage' => 'financial'
            ]);
        }
        
        if(!$project->isAnalysable()) {
            return $this->redirectToRoute('project_update',['token' => $project->getToken()]);
        }
        
        $financial = $this->getProjectFinancial($project);

        $form = $this->createForm(FinancialType::class, $financial);

        $formTax = $this->createForm(TaxType::class, $financial->createTax());

        /*dump($project->getKit()->countModules());
        dump($project->getKit()->getElementServices());
        dump($project->getTotalModules());
        dump($project->getElementServices());
        die;*/

        /*foreach ($project->getElementItems() as $item){
            dump($item);
        }
        die;*/

        return $this->render('financial.analysis', [
            'project' => $project,
            'financial' => $financial,
            'form' => $form->createView(),
            'form_tax' => $formTax->createView()
        ]);
    }

    /**
     * @Route("/{token}/calculate", name="financial_calculate")
     */
    public function calculateAction(Request $request, ProjectFinancial $financial)
    {
        $response = ['error' => 'Unprocessed metadata'];

        if ($request->isMethod('post')) {

            $form = $this->createForm(FinancialType::class, $financial);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                FinancialAnalyzer::analyze($financial);

                $this->getFinancialManager()->save($financial);

            } else {
                $response['error'] = 'Invalid form data';
            }
        }

        $this->clearTemplateCache('financial.info');

        if ($financial->getAccumulatedCash()) {
            $response = [
                'data' => $financial->getAccumulatedCash(),
                'info' => $this->renderView('financial.info', [ 'financial' => $financial ])
            ];
        }

        if($request->isXmlHttpRequest()) {
            return $this->jsonResponse($response);
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("/{token}/taxes", name="financial_taxes")
     */
    public function taxesAction(Request $request, ProjectFinancial $financial)
    {
        return $this->jsonResponse([
            'content' => $this->renderView('financial.taxes', ['financial' => $financial])
        ]);
    }

    /**
     * @Route("/tax/{token}/create", name="financial_tax_create")
     */
    public function createTaxAction(Request $request, ProjectFinancial $financial)
    {
        $tax = $financial->createTax();

        return $this->handleTaxApplication($request, $tax);
    }

    /**
     * @Route("/tax/{token}/update", name="financial_tax_update")
     */
    public function updateTaxAction(Request $request, Tax $tax)
    {
        return $this->handleTaxApplication($request, $tax);
    }
    
    /**
     * @Route("/tax/{token}/delete", name="financial_tax_delete")
     * @Method("delete")
     */
    public function deleteTaxAction(Request $request, Tax $tax)
    {
        // For errors
        /* return $this->jsonResponse([
             'error' => 'This is a custom error'
         ], Response::HTTP_OK);*/

        $manager = $this->getDoctrine()->getManager();

        $manager->remove($tax);
        $manager->flush();

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @param Tax $tax
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function handleTaxApplication(Request $request, Tax $tax)
    {
        $financial = $tax->getFinancial();

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

            if ($financial->getFinalPrice() <= 0.01) {

                $status = Response::HTTP_CONFLICT;
                $data = [
                    'error' => $this->get('translator')->trans('financial.error.negative_selling_price'),
                    'total' => $financial->getFinalPrice(),
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

            return $this->jsonResponse($data, $status);
        }

        return $this->jsonResponse([], Response::HTTP_NO_CONTENT);
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

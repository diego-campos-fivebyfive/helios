<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Service\Support\Project\FinancialAnalyzer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Financial\ProjectFinancial;
use AppBundle\Entity\Project\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Projects", route={"name"="project_index"})
 *
 * @Route("/project/{token}/proposal")
 */
class ProjectProposalController extends AbstractController
{
    /**
     * @Breadcrumb("Proposta - N&deg; {project.number}")c
     * @Route("/", name="project_proposal")
     */
    public function configAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        if($project->isDone()){

            $event = null;
            if(null != $weId = $request->get('woopra_event')){
                $event = $this->getWoopraManager()->getEvent($weId);
            }

            return $this->render('project.done', [
                'project' => $project,
                'stage' => $request->get('stage'),
                'woopraEvent' => $event
            ]);
        }
        
        if(!$project->isAnalysable()){
            return $this->redirectToRoute('project_update',['token' => $project->getToken()]);
        }

        $financial = $this->getProjectFinancial($project);

        if(!$financial->allowProposal()){
            return $this->redirectToRoute('project_financial',['token' => $project->getToken()]);
        }

        $this->handleHttpReferer($request, $financial);


        $this->checkProposal($financial);

        $projectChartData = json_encode($project->getMonthlyProduction());
        $financialChartData = json_encode($financial->getAccumulatedCash());

        $parameters = $this->getDocumentHelper()->loadFromAccount($this->getCurrentAccount());
        $chartColor = $parameters->get('chart_color');

        //$this->dd($financial->getProposal()->getSections());

        return $this->render('financial.proposal', [
            'project' => $project,
            'financial' => $financial,
            'project_chart_data' => $projectChartData,
            'financial_chart_data' => $financialChartData,
            'chart_color' => $chartColor
        ]);
    }

    /**
     * @Route("/section/{id}/update", name="proposal_section_update")
     * @Method("post")
     */
    public function updateSectionAction(Request $request, Document $document)
    {
        if($request->isXmlHttpRequest()) {

            $data = $request->request->all();

            $title = $data['title'];
            $content = $data['content'];

            $document
                ->setTitle($title)
                ->setContent($content);

            $this->getProposalHelper()->save($document);

            return $this->jsonResponse([], Response::HTTP_ACCEPTED);
        }

        return $this->jsonResponse([], Response::HTTP_CONFLICT);
    }

    /**
     * @Route("/update", name="project_proposal_update")
     * @Method("post")
     */
    public function updateAndGenerate(Request $request, Project $project)
    {
        $this->checkAccess($project);

        $data = $request->request->all();
        $projectDefaultChartData = $data['project_default_chart_data'];
        $projectChartData = $data['project_chart_data'];
        $financialDefaultChartData = $data['financial_default_chart_data'];
        $financialChartData = $data['financial_chart_data'];

        $projectManager = $this->manager('project');

        $project
            ->setDefaultChartData($projectDefaultChartData)
            ->setChartData($projectChartData);

        $projectManager->save($project);

        $financialManager = $this->getFinancialManager();

        $financial = $financialManager->fromProject($project);

        $financial
            ->setChartData($financialChartData)
            ->setDefaultChartData($financialDefaultChartData);

        $financialManager->save($financial);

        return $this->redirectToRoute('project_proposal_generate', [
            'token' => $project->getToken(),
            'display' => $data['display']
        ]);
    }

    /**
     * @Route("/generate/{display}", name="project_proposal_generate")
     */
    public function generateAction(Request $request, Project $project, $display)
    {
        $this->checkAccess($project);

        $financial = $this->getFinancialManager()->fromProject($project);
        $proposal = $financial->getProposal();
        $parameters =  $this->getDocumentHelper()->loadFromAccount($this->account());
        
        $cover = $parameters->get('cover_image');

        $title = str_replace('.pdf', '',$project->parseFilename());
        
        $mpdf = new \mPDF('', 'A4', '12', 'arial');
        $mpdf->setAutoTopMargin = false;
        $mpdf->SetTopMargin(15);
        $mpdf->SetTitle($title);

        //dump($project->getKit()); die;

        $response = $this->render('financial.document', [
            'project' => $project,
            'proposal' => $proposal,
            'financial' => $financial,
            'cover' => $cover,
            'header_logo' => $parameters->get('header_logo'),
            'header_text' => nl2br($parameters->get('header_text')),
            'background' => $parameters->get('section_title_background'),
            'color' => $parameters->get('section_title_color'),
            'parameters' => $parameters
        ]);

        $content = $response->getContent();

        switch($display) {
            case 'preview':

                $mpdf->SetWatermarkText('Pré-Visualização');
                $mpdf->showWatermarkText = true;
                $mpdf->WriteHTML($content);
                $mpdf->Output();

                die;

                break;

            case 'file':

                $mpdf->WriteHTML($content);

                $filename = $project->parseFilename();

                $project->setMetadata('filename', $filename);

                $projectManager = $this->manager('project');
                // TODO: Fix error update parameters
                $projectManager->getEntityManager()->detach($parameters);

                $explorer = $this->getFileExplorer();

                $mpdf->Output($explorer->getTmpDir($filename), 'F');

                $explorer->moveTmpFile($filename);

                $this->generateProjectSnapshot($project);
                $this->manager('project')->save($project);

                $this->getNotificationGenerator()->proposalIssued($project);

                $event = $this->createWoopraEvent('emissao proposta', [
                    'preco' => $project->getPrice(),
                    'potencia' => $project->getPower(),
                    'numero' => $project->getNumber()
                ]);

                return $this->redirectToRoute('project_proposal', [
                    'token' => $project->getToken(),
                    'stage' => 'proposal',
                    'woopra_event' => $event->getId()
                ]);

                break;

            default:
                throw $this->createNotFoundException('Display option not allowed');
                break;
        }
    }

    /**
     * @Route("/download/{force}", name="project_proposal_download", defaults={"force":false})
     */
    public function downloadAction(Project $project, $force)
    {
        $this->checkAccess($project);

        if(null != $filename = $project->getMetadata('filename')){
            $force = (bool) $force;

            $member = $project->getMember();
            $account = $member->getAccount();

            $dir = $this->get('kernel')->getRootDir() . '/../storage/files/' . $account->getEmail() . '/';

            $finder = new Finder();

            $finder->in($dir)->files()->name($filename);

            if($finder->count()) {

                $explorer = $this->getFileExplorer();

                $files = iterator_to_array($finder->getIterator());

                $file = array_values($files)[0];

                return $force ? $explorer->download($file) : $explorer->show($file) ;
            }

            //$response = $force ? $explorer->download($project) : $explorer->show($project) ;
            ///return $response;
        }

        throw $this->createNotFoundException('File not found');
    }


    /**
     * Handle pdf fixed sections
     *
     * @Route("/handle-section", name="proposal_handle_section")
     */
    public function handleSectionAction(Document $section, $parameters)
    {
        $original = str_replace(
            ['<div align', '</div>'],
            ['<p align', '</p>'],
            $section->getContent()
        );

        $par = explode('</p>', $original);
        $content = "";

        foreach($par as $p){

            preg_match('/(left|center|right|justify)/', $p, $matches);

            $align = !empty($matches) ? $matches[0] : 'left';

            $content .= '<tr><td align="'.$align.'">' . $p . '</p></td></tr>';
        }

        $section->setContent($content);

        return $this->render('financial.document_section', [
            'section' => $section,
            'parameters' => $parameters
        ]);
    }

    /**
     * @param Request $request
     * @param ProjectFinancial $financial
     */
    private function handleHttpReferer(Request $request, ProjectFinancial &$financial)
    {
        $project = $financial->getProject();

        $updateUrl = $this->generateUrl('project_update',['token' => $project->getToken()], 0);
        $refererUrl = $request->server->get('HTTP_REFERER');

        if($updateUrl == $refererUrl){

            FinancialAnalyzer::analyze($financial);

            $this->getFinancialManager()->save($financial);
        }
    }

    /**
     * @param Project $project
     */
    private function generateProjectSnapshot(Project &$project)
    {
        $em = $this->getDoctrine()->getManager();
        foreach($project->getInverters() as $projectInverter) {
            $projectInverter->clearRelations();
            $em->persist($projectInverter);
        }
        $em->flush();
        
        $project->clearRelations();
    }

    /**
     * @param ProjectFinancial $financial
     */
    private function checkProposal(ProjectFinancial &$financial)
    {
        $this->getProposalHelper()->load($financial);
    }

    /**
     * @param Project $project
     * @return \AppBundle\Entity\Financial\ProjectFinancialInterface
     * @throws \Exception
     */
    private function getProjectFinancial(Project $project)
    {
        return $this->getFinancialManager()->fromProject($project);
    }

    /**
     * @return \AppBundle\Entity\Financial\ProjectFinancialManager
     */
    private function getFinancialManager()
    {
        return $financialManager = $this->get('app.project_financial');
    }

    /**
     * @return \AppBundle\Service\ProposalHelper $proposalHelper
     */
    private function getProposalHelper()
    {
        return $this->get('app.proposal_helper');
    }

    /**
     * Check all authorizations levels
     * @param $target
     */
    private function checkAccess($target)
    {
        $this->get('app.project_authorization')->isAuthorized($target);
    }
}
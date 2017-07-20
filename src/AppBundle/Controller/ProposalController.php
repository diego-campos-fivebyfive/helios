<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/proposal")
 */
class ProposalController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Proposal:index.html.twig', array(

        ));
    }

    /**
     * @Route("/{id}/save", name="proposal_save")
     */
    public function saveContentAction(Request $request, Project $project){
        $content = $request->request->get('content');

        $project->setProposal($content);

        $this->manager('project')->save($project);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/editor", name="proposal_editor")
     */
    public function editorAction(Project $project)
    {
        //$customer = $project->getCustomer()->getUser()->getEmail();
        //dump($project);die();

        $projectFake = [
            'customer' => [
                'generationChart' => [20,550,347,805,474,647,864,275,759,584,967,847]
            ]
        ];


        return $this->render('AppBundle:Proposal:editor.html.twig',
            [
                'project' => $project,
                'projectFake' => $projectFake,
            ]);
    }

    /**
     * @Route("/pdf", name="proposal_pdf_legacy")
     */
    public function pdfAction()
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', array(

        ));
    }

    /**
     * @Route("/{id}/proposalPDF", name="proposal_pdf")
     */
    public function proposalPDFAction(Project $project)
    {
        return $this->render('AppBundle:Proposal:proposalPDF.html.twig', [
            'project' => $project
        ]);
    }

}

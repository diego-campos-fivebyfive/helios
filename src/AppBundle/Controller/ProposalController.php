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


       // dump($project->getProjectExtras()->first()->getExtra());die();
        $projectFake = [
            'customer' => [
                'generationChart' => [560,550,447,405,374,347,364,405,459,504,567,597]
            ]
        ];


        return $this->render('AppBundle:Proposal:editor.html.twig',
            [
                'project' => $project,
                'projectFake' => $projectFake,
            ]);
    }


    /**
     * @Route("/{id}/pdf", name="proposal_pdf")
     */
    public function pdfAction(Project $project)
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', [
            'project' => $project
        ]);
    }

}

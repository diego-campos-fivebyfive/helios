<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $this->render('AppBundle:Proposal:editor.html.twig',
            [
                'project' => $project
            ]);
    }

    /**
     * @Route("/{id}/generator", name="proposal_pdf_generator")
     */
    public function generatorAction($id)
    {
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('viewport-size', '1280x1024');

        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $filename = md5(uniqid(time())) . '.pdf';
        $file = $dir.$filename;

       // $url = $this->generateUrl('files_pdf',['id'=>$id]);
        //dump($url);die;
        $url = "http://54.233.150.10/public/files/306/pdf";

        try {
            $snappy->generate($url, $file);
            if(file_exists($file)){
                return $this->json([
                    'filename' => $filename
                ], Response::HTTP_OK);
            }else{
                return $this->json([
                    'error' => 'File not found.'
                ], Response::HTTP_NOT_FOUND);
            }
        }
        catch(\Exception $error) {
            return $this->json([
                'error' => 'Could not generate PDF.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/display/{filename}", name="proposal_display_pdf")
     */
    public function displayAction($filename)
    {
        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $file = $dir.$filename;
        if(file_exists($file)){
            return new BinaryFileResponse($file);
        }else{
            die("Arquivo não encontrado");
        }

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

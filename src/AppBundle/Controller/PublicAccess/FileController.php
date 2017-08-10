<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\Project;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("files")
 */
class FileController extends AbstractController
{

    /**
     * @Route("/{id}/pdf", name="files_pdf")
     */
    public function pdfGeneratorAction(Project $project)
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/dentro", name="files_dentro")
     */
    public function dentroAction()
    {

        $snappy = $this->get('knp_snappy.pdf');

        $snappy->setOption('viewport-size', '1280x1024');

        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $filename = md5(uniqid(time())) . '.pdf';

        $url = 'http://54.233.150.10/public/files/306/pdf';

        try {
            $snappy->generate($url, $dir . $filename);
            $file = $dir.$filename;

            if(file_exists($file)){
                return new BinaryFileResponse($file);
            }

            die('O arquivo nao existe');
        }
        catch(\Exception $error) {
            die($error);
            //return Response::HTTP_INTERNAL_SERVER_ERROR;
            //ignore
        }
    }

    /**
     * @Route("/fora", name="files_fora")
     */
    public function foraAction()
    {

        $snappy = $this->get('knp_snappy.pdf');

        $snappy->setOption('viewport-size', '1280x1024');

        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $filename = md5(uniqid(time())) . '.pdf';

        $url = 'http://www.statusimagens.com/whatsapp/imagens';



        try {
            $snappy->generate($url, $dir . $filename);

            $file = $dir.$filename;

            if(file_exists($file)){
                return new BinaryFileResponse($file);
            }

            die('O arquivo nao existe');
        }
        catch(\Exception $error) {
            die($error);
            //return Response::HTTP_INTERNAL_SERVER_ERROR;
            //ignore
        }
    }


    /**
     * @Route("/{token}/proposal", name="file_proposal")
     */
    public function proposalAction(Project $project)
    {
        $member = $project->getMember();
        $account = $member->getAccount();

        $token = md5(uniqid(time()));

        $file = $this->get('kernel')->getRootDir() . sprintf(
                '/../storage/files/%s/%s/%s',
                $account->getEmail(),
                $member->getEmail(),
                $project->getMetadata('filename')
            );

        $files = $this->getStore('proposal_files');

        $files[$token] = base64_encode($file);

        $this->store('proposal_files', $files);

        $tag = sprintf('Proposta - %s', $project->getNumber());

        $project->setMetadata('email', ($project->getMetadata('email', 0) + 1));
        $this->getProjectManager()->save($project);

        return $this->redirectToRoute('files_download', [
            'token' => $token,
            'tag' => $tag
        ]);
    }

    /**
     * @Route("/{token}/v/{tag}", name="files_download", defaults={"tag":"Visualizar Arquivo"})
     */
    public function downloadOrViewAction($token)
    {
        $files = $this->getStore('proposal_files');

        if(array_key_exists($token, $files)){

            $file = base64_decode($files[$token]);

            if(file_exists($file)){
                return new BinaryFileResponse($file);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @Route("/pdf", name="file_proposal_pdf")
     */
    public function pdfAction()
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', array());
    }

    /**
     * @Route("/snappy")
     */
    public function snappyAction()
    {
        /** @var LoggableGenerator $snappy */
        $snappy = $this->get('knp_snappy.pdf');

        //$snappy->setOption('zoom', 4);
        $snappy->setOption('viewport-size', '1280x1024');

        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $filename = md5(uniqid(time())) . '.pdf';

        $url = $this->generateUrl('file_proposal_pdf',[],0);

        $snappy->generate($url, $dir . $filename);
    }

    /**
     * @param $id
     * @return array
     */
    private function getStore($id)
    {
        return $this->restore($id, [], false);
    }
}

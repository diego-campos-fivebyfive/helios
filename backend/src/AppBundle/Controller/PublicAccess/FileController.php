<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Theme;
use Buzz\Message\Request;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

/**
 * @Route("files")
 */
class FileController extends AbstractController
{
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
        $this->manager('project')->save($project);

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
     * @Route("/{id}/pdf", name="proposal_pdf")
     */
    public function pdfAction(Theme $theme)
    {
        $content = str_replace(
            ['contenteditable="true"'],
            [''],
            $theme->getContent()
        );

        return $this->render('AppBundle:Proposal:_pdf.html.twig', [
            'theme' => $theme,
            'content' => $content
        ]);
    }

    /**
     * @Route("/process", name="file_process")
     */
    public function processAction()
    {
        // ./wkhtmltopdf --viewport-size 1280x1024 --zoom 2 http://kolinalabs.com/dev/pdf/pdf.html exemplo.pdf
        //ini_set('max_execution_time', );

        $binary = $this->getParameter('knp_snappy.pdf.binary');

        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $filename = md5(uniqid(time())) . '.pdf';

        $output = $dir . $filename;

        $command = sprintf('%s --viewport-size 1280x1024 --zoom 2 https://kolinalabs.com/dev/pdf/pdf.html %s', $binary, $output);

        $process = new Process($command);

        $process->run();

        dump($process->getOutput()); die;
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

<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Theme;
use AppBundle\Entity\Order\Order;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/{id}/proforma", name="proforma_pdf")
     */
    public function proformaAction(Order $order)
    {
        $initialPages = 0;
        foreach ($order->getChildrens() as $children){
            $initialPages++;
            if ($children->getInsurance())
                $initialPages++;
        }

        return $this->render('admin/orders/proforma/index.html.twig', array(
            'order' => $order,
            'initialPages' => $initialPages
        ));
    }

    /**
     * @Route("/push_s3_files", name="push_s3")
     * @Method("POST")
     */
    public function pushS3Action(Request $request)
    {
        if (!$this->getAuth($request)) {
            return $this->json([]);
        }

        $filesName = json_decode($request->getContent(), true);

        $path = "{$this->container->get('kernel')->getRootDir()}/../../.uploads/fiscal/danfe";

        foreach ($filesName['names'] as $fileName) {
            $file = "{$path}/{$fileName}";
            $options = $this->getS3Options($fileName);
            $this->container->get('app_storage')->push($options, $file);
        }

        return $this->json([]);
    }

    /**
     * @param $filename
     * @return array
     */
    private function getS3Options($filename)
    {
        return [
            'filename' => $filename,
            'root' => 'fiscal',
            'type' => 'danfe',
            'access' => 'private'
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function getAuth(Request $request)
    {
        $auth = "OewkQ42mCxVyfk7cbKg5jORFTWdWMQhxIO2bjHQt";
        $secret = "NXTh0oqmwed4PvK3HCysMJjMWEGGJ2Fw0hXDfyox";
        $header = $request->server->getHeaders();

        return $header['AUTHORIZATION'] === $auth && $header['SECRET'] === $secret;
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

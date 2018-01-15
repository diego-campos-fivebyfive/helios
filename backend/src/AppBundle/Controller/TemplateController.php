<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\ThemeInterface;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/template")
 */
class TemplateController extends AbstractController
{
    /**
     * @Breadcrumb("Templates")
     * @Route("/", name="template")
     */
    public function indexAction()
    {
        return $this->render('projects/templates/index.html.twig');
    }

    /**
     * @Route("/upload", name="template_upload")
     */
    public function templateUploadAction(Request $request)
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return $this->render('projects/templates/upload.html.twig');
        }

        $filename = substr(md5(uniqid(rand(1,6))), 0, 8) . '.docx';

        $options = [
            'filename' => $filename,
            'root' => 'proposal',
            'type' => 'template',
            'access' => 'private'
        ];

        $originalFilename = $file->getClientOriginalName();

        $this->saveTheme($filename, $originalFilename);

        $location = $this->container->get('app_storage')->location($options);

        $path = str_replace($filename, '', $location);

        $file->move($path, $filename);

        return $this->json([ 'name' => $filename ]);
    }

    /**
     * @Route("/templatesList", name="templates_list")
     */
    public function templatesList()
    {
        $themes = $this->manager('theme')->findBy([
            'accountId' => $this->account()->getId()
        ]);

        return $this->render('projects/templates/templates_list.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/processor", name="template_processor")
     */
    public function replaceTemplateAction()
    {
        /** @var ProjectInterface $project */
        $project = $this->manager('project')->find(2253);

        $path = $this->container->get('kernel')->getRootDir();
        $templatPath = $path . '/cache/template.docx';

        $template = $this->getTemplateProcessor()->process($project, $templatPath);

        $encodeTemplate = base64_encode($template);

        //dump($template);die;
        return $this->redirectToRoute('download_template', [
            'template' => $encodeTemplate
        ]);
    }

    /**
     * @Route("/download/{template}/", name="download_template")
     */
    public function downloadTemplateAction($template)
    {
        $template = base64_decode($template);

        $header = ResponseHeaderBag::DISPOSITION_ATTACHMENT;

        $response = new BinaryFileResponse($template, Response::HTTP_OK, [], true, $header);

        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @param string $filename
     * @param string $originalName
     */
    private function saveTheme(string $filename, string $originalName)
    {
        $manager = $this->manager('theme');

        /** @var ThemeInterface $theme */
        $theme = $manager->create();
        $theme
            ->setAccountId($this->account()->getId())
            ->setTheme(1)
            ->setContent('')
            ->setName($originalName)
            ->setFilename($filename);

        $manager->save($theme);
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface|\AppBundle\Entity\AccountInterface
     */
    protected function account()
    {
        return $this->member()->getAccount();
    }


    /**
     * @return \AppBundle\Service\Proposal\WordProcessor
     */
    private function getTemplateProcessor()
    {
        return $this->get('word_processor');
    }
}

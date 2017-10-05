<?php

namespace AppBundle\Controller;

use Aws\S3\S3Client;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Theme;
use AppBundle\Service\Editor\Formatter;
use AppBundle\Service\Editor\Normalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * @Route("/proposal")
 * @Breadcrumb("Proposal")
 */
class ProposalController extends AbstractController
{
    /**
     * @Route("/{id}/save", name="proposal_save")
     */
    public function saveContentAction(Request $request, Project $project)
    {
        $currentTheme = $this->manager('theme')->findOneBy([
            'accountId' => $project->getMember()->getAccount()->getId(),
            'theme' => 1
        ]);
        /** @var Theme $theme */
        $theme;

        if($currentTheme){
            $theme = $currentTheme;
            $theme->setTheme(0);
            $this->manager('theme')->save($theme);
        }

        if (!$project->getProposal()){
            $manager = $this->manager('theme');
            $theme = $manager->create();
            $theme->setAccountId($project->getMember()->getAccount()->getId());
        }else{
            $manager = $this->manager('theme')->find($project->getProposal());
            $theme = $manager;
        }

        $content = $request->request->get('content');

        $theme->setTheme(1);
        $theme->setContent($content);
        $this->manager('theme')->save($theme);

        $project->setProposal($theme->getId());
        $this->manager('project')->save($project);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/editor", name="proposal_editor")
     */
    public function editorAction(Project $project)
    {
        if(empty($project->getAccumulatedCash())){
            return $this->render('proposal.alerts', [
                'error' => 'empty_calculation_metadata',
                'project' => $project,
            ]);
        }

        $this->denyAccessUnlessGranted('edit', $project);

        $theme = $this->resolveTheme($project);

        //echo $theme->getContent(); die;

        $content = $theme->getContent();

        $data = Formatter::format($project);

        $components = $this->renderView('proposal.components', [
            'project' => $project
        ]);

        $data['components'] = [
            'label' => 'Equipamentos e Serviços',
            'value' => $components,
            'handle' => 'static'
        ];

        Normalizer::prepare($content, $data);

        return $this->render('AppBundle:Proposal:_editor.html.twig',[
            'project' => $project,
            'theme' => $theme,
            'content' => $content,
            'data' => $data
        ]);
    }

    /**
     * @Route("/{id}/save", name="proposal_save")
     */
    public function saveAction(Project $project, Request $request)
    {
        $content = $request->get('content');

        $manager = $this->manager('theme');

        $theme = $this->resolveTheme($project);

        $theme
            ->setTheme(true)
            ->setContent($content)
            ->setAccountId($this->account()->getId())
        ;

        $manager->save($theme);

        return $this->json();
    }

    private function resolveTheme(Project $project)
    {
        $manager = $this->manager('theme');

        /** @var Theme $theme */
        $theme = $manager->findOneBy([
            'accountId' => $this->account()->getId(),
            'theme' => 1
        ]);

        if (!$theme){

            $theme = $manager->findOneBy([
                'accountId' => null,
                'theme' => 1
            ]);

            if(!$theme){

                $theme = $manager->create();
                $theme->setAccountId(null);
                $theme->setTheme(1);
                $theme->setContent('');

                $manager->save($theme);
            }
        }

        return $theme;
    }

    /**
     * @Route("/{id}/print", name="proposal_print")
     */
    public function printAction(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);

        $theme = $this->resolveTheme($project);

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
     * @Route("/{id}/generator", name="proposal_pdf_generator")
     */
    public function generatorAction(Project $project)
    {
        $theme = $this->resolveTheme($project);

        if(!$theme) {
            return $this->json([], Response::HTTP_NO_CONTENT);
        }

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('viewport-size', '1280x1024');
        $snappy->setOption('margin-top', 0);
        $snappy->setOption('margin-bottom', 0);
        $snappy->setOption('margin-left', 0);
        $snappy->setOption('margin-right', 0);
        $snappy->setOption('zoom', 2);

        $PATH = "{$this->get('kernel')->getRootDir()}/../..";
        $tempFileName = md5(uniqid()) . '.pdf';
        $tempFile = "{$PATH}/.uploads/proposal/{$tempFileName}";

        $absoluteUrl = UrlGeneratorInterface::ABSOLUTE_URL;
        $snappyUrl = $this->generateUrl('proposal_pdf', [ 'id' => $theme->getId() ], $absoluteUrl);

        $snappy->generate($snappyUrl, $tempFile);

        if (!file_exists($tempFile)) {
            return $this->json([ 'filename' => $tempFileName ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$project->getIssuedAt()) {
            $manager = $this->manager('project');
            $project->setIssuedAt(new \DateTime('now'));
            $manager->save($project);
        }

        $s3 = $this->get('aws.s3');
        $s3->putObject([
            'Bucket' => 'pss-geral',
            'Key' => "proposal/$tempFileName",
            'Body' => fopen($tempFile, 'rb'),
            'ACL' => 'public-read'
        ]);

        return $this->json([ 'filename' => $tempFileName ], Response::HTTP_OK);
    }

    /**
     * @Route("/display/{tempFileName}", name="proposal_display_pdf")
     */
    public function displayAction($tempFileName)
    {
        $PATH = "{$this->get('kernel')->getRootDir()}/../..";
        $tempFile = "{$PATH}/.uploads/proposal/{$tempFileName}";

        if (file_exists($tempFile)) {
            return new BinaryFileResponse(new File($tempFile));
        }

        return $this->json([ 'error' => 'File not found.' ], Response::HTTP_NOT_FOUND);
    }
}

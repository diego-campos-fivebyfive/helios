<?php

namespace AppBundle\Controller;

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

        $status = Response::HTTP_CONFLICT;
        $filename = null;

        if($theme) {

            $snappy = $this->get('knp_snappy.pdf');
            $snappy->setOption('viewport-size', '1280x1024');
            $snappy->setOption('margin-top', 0);
            $snappy->setOption('margin-bottom', 0);
            $snappy->setOption('margin-left', 0);
            $snappy->setOption('margin-right', 0);
            $snappy->setOption('zoom', 2);

            $dir = $this->get('kernel')->getRootDir() . '/../storage/';
            $filename = md5(uniqid(time())) . '.pdf';
            $file = $dir . $filename;

            $url = $this->generateUrl('proposal_pdf', ['id' => $theme->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            try {

                $snappy->generate($url, $file);

                if (file_exists($file)) {
                    $status = Response::HTTP_OK;

                    if (!$project->getIssuedAt()) {
                        $manager = $this->manager('project');
                        $project->setIssuedAt(new \DateTime('now'));
                        $manager->save($project);
                        $member = $project->getMember();

                        $this->get('notifier')->notify([
                            'Evento' => '509',
                            'Callback' => 'proposal_issued',
                            'Body' => [
                                'Valor' => $project->getCostPrice(),
                                'Empresa' => $member->getAccount()->getFirstname(),
                                'Contato' => $member->getFirstname()
                            ]
                        ]);

                    }
                }

            } catch (\Exception $error) {
            }
        }

        return $this->json([
            'filename' => $filename
        ], $status);
    }

    /**
     * @Route("/display/{filename}", name="proposal_display_pdf")
     */
    public function displayAction($filename)
    {
        $dir = $this->get('kernel')->getRootDir() . '/../storage/';
        $file = $dir.$filename;

        if(file_exists($file)){

            return new BinaryFileResponse(new File($file));
        }else{
            return $this->json(
                ['error' => 'File not found.'],
                Response::HTTP_NOT_FOUND);
        }

    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Theme;
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
 * @Route("/project")
 */
class TemplateController extends AbstractController
{
    private $defaultTheme = 'template_default.docx';

    /**
     * @Breadcrumb("Templates")
     * @Route("/{id}/manage", name="project_template")
     */
    public function indexAction(Project $project)
    {
        $this->checkAccess($project);

        return $this->render('projects/templates/index.html.twig', [
            'project' => $project
        ]);
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
            'type' => 'theme',
            'access' => 'private'
        ];

        $originalFilename = $file->getClientOriginalName();

        $this->saveTheme($filename, $originalFilename);

        $this->container->get('app_storage')->push($options, $file);

        return $this->json([ 'name' => $filename ]);
    }

    /**
     * @Route("/{project}/list", name="templates_list")
     */
    public function templatesListAction(Project $project)
    {
        $themes = $this->manager('theme')->findBy([
            'accountId' => $this->account()->getId(),
            'theme' => false
        ]);

        return $this->render('projects/templates/templates_list.html.twig', [
            'themes' => $themes,
            'project' => $project,
            'default' => $this->defaultTheme,
        ]);
    }

    /**
     * @Route("/{project}/processor/{theme}", name="template_processor")
     */
    public function replaceTemplateAction(Project $project, $theme)
    {
        if($this->defaultTheme !== $theme){
            /** @var Theme $theme */
            $theme = $this->manager('theme')->find($theme);
        }

        $filename = $theme instanceof Theme ? $theme->getFilename() : $theme ;

        $options = array(
            'filename' => $filename,
            'root' => 'proposal',
            'type' => 'theme',
            'access' => 'private'
        );

        $templatePath = $this->get('app_storage')->display($options);

        $template = $this->getTemplateProcessor()->process($project, $templatePath);

        if ($template) {
            $project->setIssuedAt(new \DateTime('now'));
            $this->manager('project')->save($project);
        }

        $encodeTemplate = base64_encode($template);

        return $this->redirectToRoute('download_template', [
            'project' => $project->getId(),
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
     * @Route("/view/{theme}/", name="view_theme")
     */
    public function viewTemplateAction($theme)
    {
        $options = $this->optionsTheme($theme);

        $templatePath = $this->get('app_storage')->display($options);

        $header = ResponseHeaderBag::DISPOSITION_ATTACHMENT;

        $response = new BinaryFileResponse($templatePath, Response::HTTP_OK, [], true, $header);

        return $response;
    }

    /**
     * @Route("/{theme}/delete", name="delete_theme")
     */
    public function deleteTemplateAction(Theme $theme)
    {
        $options = $this->optionsTheme($theme);

        $this->get('app_storage')->remove($options);

        $this->manager('theme')->delete($theme);

        return $this->json([]);
    }

    /**
     * @Route("/tags", name="tags_list")
     */
    public function tagsListAction()
    {
        $tags = $this->getTagsGroups();

        return $this->render('projects/templates/tags_list.html.twig', [
            'tagsList' => $tags
        ]);
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
            ->setTheme(false)
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

    /**
     * @return array
     */
    private function getTagsGroups()
    {
        return $tags = [
            'Dados Gerais' => [
                'itens' => [
                    ['tag' => '${ProjetoNumero}', 'description' =>'Nº do Projeto'],
                    ['tag' => '${ProjetoPotencia}', 'description' =>'Potência do Projeto']
                ]
            ],
            'Dados do Cliente' => [
                'itens' => [
                    ['tag' => '${ClienteNome}', 'description' =>'Nome do Cliente'],
                    ['tag' => '${ClienteDocumento}', 'description' =>'CPF/CNPJ do Cliente'],
                    ['tag' => '${ClienteTelefone}', 'description' =>'Telefone do Cliente'],
                    ['tag' => '${ClienteEmail}', 'description' =>'E-mail do Cliente']
                ]
            ],
            'Dados de Geracão' => [
                'itens' => [
                    ['tag' => '${GeracaoAnual}', 'description' =>'Geração Anual'],
                    ['tag' => '${GeracaoMediaMensal}', 'description' =>'Geração Média Mensal'],
                    ['tag' => '${mes}', 'description' =>'Mês referente a Geração (necessário inserir em tabela)'],
                    ['tag' => '${geracao}', 'description' =>'kWh Gerado no mês (necessário inserir em tabela)']
                ]
            ],
            'Análise Financeira' => [
                'itens' => [
                    ['tag' => '${PropostaValor}', 'description' =>'Valor da Proposta'],
                    ['tag' => '${TempoDeVida}', 'description' =>'Tempo de Vida do projeto'],
                    ['tag' => '${Inflacao}', 'description' =>'Inflação anual'],
                    ['tag' => '${PerdaEficiencia}', 'description' =>'Perda de Eficiência ao longo da vida'],
                    ['tag' => '${CustoAnualOperacao}', 'description' =>'Custo Anual de Operacão'],
                    ['tag' => '${PrecoKwhImpostos}', 'description' =>'Preço atual Kwh + Impostos'],
                    ['tag' => '${CaixaAcumulado}', 'description' =>'Caixa Acumulado'],
                    ['tag' => '${ValorPresenteLiquido}', 'description'  => 'Valor Presente Liquido'],
                    ['tag' => '${TaxaRetorno}', 'description' =>'Taxa de Retorno'],
                    ['tag' => '${PaybackSimples}', 'description' =>'Payback Simples'],
                    ['tag' => '${PaybackDescontado}', 'description' =>'Payback Descontado'],
                    ['tag' => '${ano}', 'description' =>'Ano do caixa acumulado (necessário inserir em tabela)'],
                    ['tag' => '${valor}', 'description' =>'Valor anual do caixa acumulado (necessário inserir em tabela)']
                ]
            ],
            'Equipamentos' => [
                'itens' => [
                    ['tag' => '${titulo}', 'description' =>'Tipo do equipamento (necessário inserir em tabela)'],
                    ['tag' => '${descricao}', 'description' =>'Descrição do Equipamento (necessário inserir em tabela)'],
                    ['tag' => '${quantidade}', 'description' =>'Quantidade de Produtos (necessário inserir em tabela)']
                ]
            ]
        ];
    }

    /**
     * @param $theme
     * @return array
     */
    private function optionsTheme($theme)
    {
        if($this->defaultTheme !== $theme){
            /** @var Theme $theme */
            $theme = $this->manager('theme')->find($theme);
        }

        $filename = $theme instanceof Theme ? $theme->getFilename() : $theme ;

        $options = array(
            'filename' => $filename,
            'root' => 'proposal',
            'type' => 'theme',
            'access' => 'private'
        );

        return $options;
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

<?php

namespace AppBundle\Controller;

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
    public function templatesListAction()
    {
        $themes = $this->manager('theme')->findBy([
            'accountId' => $this->account()->getId()
        ]);

        return $this->render('projects/templates/templates_list.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/processor/{theme}/", name="template_processor")
     */
    public function replaceTemplateAction(Theme $theme)
    {
        /** @var ProjectInterface $project */
        $project = $this->manager('project')->find(2253);

        $options = array(
            'filename' => $theme->getFilename(),
            'root' => 'proposal',
            'type' => 'template',
            'access' => 'private'
        );

        $templatePath = $this->get('app_storage')->location($options);

        $template = $this->getTemplateProcessor()->process($project, $templatePath);

        $encodeTemplate = base64_encode($template);

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
     * @Route("/tags", name="tags_list")
     */
    public function tagsListAction()
    {
        $tags = $this->getTags();

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

    /**
     * @return array
     */
    private function getTags()
    {
        return $tags = array(
            ['tag' => '${ProjetoNumero}', 'description' =>'Nº do Projeto'],
            ['tag' => '${ProjetoPotencia}', 'description' =>'Potência do Projeto'],
            ['tag' => '${PropostaValor}', 'description' =>'Valor da Proposta'],
            ['tag' => '${ClienteNome}', 'description' =>'Nome do Cliente'],
            ['tag' => '${ClienteDocumento}', 'description' =>'Cpf do Cliente'],
            ['tag' => '${ClienteTelefone}', 'description' =>'Telefone'],
            ['tag' => '${ClienteEmail}', 'description' =>'E-mail'],
            ['tag' => '${GeracaoAnual}', 'description' =>'Geração Anual'],
            ['tag' => '${GeracaoMediaMensal}', 'description' =>'Geração Média Mensal'],
            ['tag' => '${TempoDeVida}', 'description' =>'Tempo de Vida'],
            ['tag' => '${Inflacao}', 'description' =>'Inflação'],
            ['tag' => '${PerdaEficiencia}', 'description' =>'Perda de Eficiência'],
            ['tag' => '${CustoAnualOperacao}', 'description' =>'Custo Anual de Operacão'],
            ['tag' => '${PrecoKwhImpostos}', 'description' =>'Preco Kwh + Impostos'],
            ['tag' => '${CaixaAcumulado}', 'description' =>'Caixa Acumulado'],
            ['tag' => '${ValorPresenteLiquido}', 'description'  => 'Valor Presente Liquido'],
            ['tag' => '${TaxaRetorno}', 'description' =>'Taxa de Retorno'],
            ['tag' => '${PaybackSimples}', 'description' =>'Payback Simples'],
            ['tag' => '${PaybackDescontado}', 'description' =>'Payback Descontado'],
            ['tag' => '${titulo}', 'description' =>'Nome da Familia do Equipamento'],
            ['tag' => '${descricao}', 'description' =>'Descrição do Equipamento'],
            ['tag' => '${quantidade}', 'description' =>'Quantidade de Produtos'],
            ['tag' => '${mes}', 'description' =>'Mês referente a Geração'],
            ['tag' => '${geracao}', 'description' =>'Valor Gerado'],
            ['tag' => '${ano}', 'description' =>'Ano referente ao acumulo de caixa'],
            ['tag' => '${valor}', 'description' =>'Valor referente a cada ano do acumulo de caixa']
        );
    }
}

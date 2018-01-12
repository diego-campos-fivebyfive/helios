<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Service\Order\ComponentExtractor;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PhpOffice\PhpWord\TemplateProcessor;

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
     * @Route("/processor", name="template_processor")
     */
    public function replaceTemplateAction()
    {
        /** @var ProjectInterface $project */
        $project = $this->manager('project')->find(2253);
        
        $components = ComponentExtractor::fromProject($project);

        $path = $this->container->get('kernel')->getRootDir();
        $file = $path . '/cache/template.docx';

        $this->templateProcessor = new TemplateProcessor($file);

        $this->templateProcessor->setValue('ProjetoNumero', $project->getNumber());
        $this->templateProcessor->setValue([
            'ProjetoPotencia', 'PropostaValor', 'ClienteNome', 'ClienteDocumento',
            'ClienteTelefone', 'ClienteEmail', 'GeracaoAnual', 'GeracaoMediaMensal',
            'TempoDeVida', 'Inflacao', 'PerdaEficiencia', 'CustoAnualOperacao',
            'PrecoKwhImpostos', 'CaixaAcumulado', 'ValorPresenteLiquido', 'TaxaRetorno',
            'PaybackSimples', 'PaybackDescontado', 'Descricao', 'Quantidade',
        ],
        array(
            $project->getPower() . ' Kwp',
            self::formatCurrency($project->getSalePrice()),
            $project->getCustomer()->getName(),
            $project->getCustomer()->getDocument(),
            $project->getCustomer()->getPhone(),
            $project->getCustomer()->getEmail(),
            round($project->getMetadata()['total']['kwh_year']) . ' kWh',
            round(($project->getMetadata()['total']['kwh_year'] / 12)). ' Kwp',
            $project->getLifetime(). ' anos',
            $project->getInflation().' %',
            $project->getEfficiencyLoss().' %',
            self::formatCurrency($project->getAnnualCostOperation()),
            self::formatCurrency($project->getEnergyPrice()),
            self::formatCurrency($project->getAccumulatedCash(true)),
            self::formatCurrency($project->getNetPresentValue()),
            $project->getInternalRateOfReturn().' %',
            self::formatPayback($project->getPaybackYears(), $project->getPaybackMonths()),
            self::formatPayback($project->getPaybackYearsDisc(), $project->getPaybackMonthsDisc())
        ));

        $groupByFamily = function($acc, $component) {
            $item = [
                "description" => $component['description'],
                "quantity" => $component['quantity']
            ];

            $acc = (is_array($acc)) ? $acc : [];

            if (!array_key_exists($component['family'], $acc)) {
                $acc[$component['family']] = [$item];
            }
            else {
                $acc[$component['family']][] = $item;
            }

            return $acc;
        };

        $familiesOfComponents = array_reduce($components, $groupByFamily, []);

        $cloneLines = count($components) + count($familiesOfComponents);

        $this->templateProcessor->cloneRow('descricao', $cloneLines);

        $familiesTranslations = [
            'module' => 'MÓDULOS',
            'inverter' => 'INVERSORES',
            'string_box' => 'STRING BOX',
            'structure' => 'ESTRUTURAS',
            'variety' => 'VARIEDADES'
        ];

        $currentLine = 1;

        foreach ($familiesOfComponents as $keyFamily => $family) {
            self::writeLineTitle($currentLine, $familiesTranslations[$keyFamily]);
            $currentLine++;

            foreach ($family as $component) {
                self::writeLineComponent($currentLine, $component);
                $currentLine++;
            }
        }

        $this->templateProcessor->cloneRow('mes', 12);

        self::writeMonthlyGenerate($project);

        $totalYears = count($project->getAccumulatedCash());

        $this->templateProcessor->cloneRow('ano', $totalYears);

        self::writeAccumulatedCash($project, $totalYears);

        $outputFile = $path . '/cache/test_docx.docx';

        $this->templateProcessor->saveAs($outputFile);

        dump($this->templateProcessor);die;
    }

    /**
     * @param $number
     * @return string
     */
    private static function formatCurrency($number)
    {
        return sprintf('%s %s',  'R$ ', number_format($number, 2, ',', '.'));
    }

    /**
     * @param $years
     * @param $months
     * @return string
     */
    private static function formatPayback($years, $months)
    {
        $formatted = '';

        if($years > 0){
            $formatted .= $years;
            $formatted .= $years > 1 ? ' anos' : ' ano';
        }

        if($months > 0){
            if($formatted) $formatted .= ' e ';
            $formatted .= $months;
            $formatted .= $months > 1 ? ' meses' : ' mês';
        }

        return $formatted;
    }

    /**
     * @param int $line
     * @param string $key
     * @param string $content
     */
    private function writeLineContent(int $line, string $key, string $content='')
    {
        $this->templateProcessor->setValue("${key}#${line}", $content);
    }

    /**
     * @param int $line
     * @param string $title
     */
    private function writeLineTitle(int $line, string $title)
    {
        self::writeLineContent($line, 'titulo', $title);
        self::writeLineContent($line, 'descricao');
        self::writeLineContent($line,'quantidade');
    }

    /**
     * @param int $line
     * @param array $component
     */
    private function writeLineComponent(int $line, array $component)
    {
       self::writeLineContent($line, 'titulo');
       self::writeLineContent($line, 'descricao', $component['description']);
       self::writeLineContent($line, 'quantidade', $component['quantity']);
    }

    /**
     * @param Project $project
     */
    private function writeMonthlyGenerate(Project $project)
    {
        $months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        for ($i = 0; $i < 12; $i++) {
            $this->templateProcessor->setValue('mes#'.($i + 1), $months[$i]);
            $this->templateProcessor->setValue('geracao#'.($i + 1), $project->getMonthlyProduction()[$i]. ' Kwp');
        }
    }

    /**
     * @param Project $project
     * @param int $totalYears
     */
    private function writeAccumulatedCash(Project $project, int $totalYears)
    {
        for ($i = 0; $i < $totalYears; $i++) {
            $this->templateProcessor->setValue('ano#'.($i +1), $i);
            $this->templateProcessor
                ->setValue('valor#'.($i + 1), self::formatCurrency($project->getAccumulatedCash()[$i]));
        }
    }
}

<?php

namespace AppBundle\Service\Proposal;

use AppBundle\Entity\Component\Project;
use AppBundle\Service\Order\ComponentExtractor;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WordProcessor
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * FileExplorer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param Project $project
     * @param string $templatePath
     * @return string
     */
    public function process(Project $project, string $templatePath)
    {
        $components = ComponentExtractor::fromProject($project);

        $this->templateProcessor = new TemplateProcessor($templatePath);

        $this->templateProcessor->setValue('ProjetoNumero', $project->getNumber());
        $this->templateProcessor->setValue([
            'ProjetoPotencia', 'PropostaValor', 'ClienteNome', 'ClienteDocumento',
            'ClienteTelefone', 'ClienteEmail', 'GeracaoAnual', 'GeracaoMediaMensal',
            'TempoDeVida', 'Inflacao', 'PerdaEficiencia', 'CustoAnualOperacao',
            'PrecoKwhImpostos', 'CaixaAcumulado', 'ValorPresenteLiquido', 'TaxaRetorno',
            'PaybackSimples', 'PaybackDescontado', 'Descricao', 'Quantidade',
        ],
            array(
                str_replace('.', ',', $project->getPower()),
                self::formatCurrency($project->getSalePrice()),
                $project->getCustomer()->getName(),
                $project->getCustomer()->getDocument(),
                $project->getCustomer()->getPhone(),
                $project->getCustomer()->getEmail(),
                round($project->getMetadata()['total']['kwh_year']),
                round(($project->getMetadata()['total']['kwh_year'] / 12)),
                $project->getLifetime(),
                $project->getInflation(),
                $project->getEfficiencyLoss(),
                self::formatCurrency($project->getAnnualCostOperation()),
                self::formatCurrency($project->getEnergyPrice()),
                self::formatCurrency($project->getAccumulatedCash(true)),
                self::formatCurrency($project->getNetPresentValue()),
                str_replace('.', ',', $project->getInternalRateOfReturn()),
                self::formatPayback($project->getPaybackYears(), $project->getPaybackMonths()),
                self::formatPayback($project->getPaybackYearsDisc(), $project->getPaybackMonthsDisc())
            ));

        try {

            $familiesOfComponents = $this->getFamilyComponents($components);

            $cloneLines = count($components) + count($familiesOfComponents);

            $this->templateProcessor->cloneRow('descricao', $cloneLines);

            $this->replaceComponents($familiesOfComponents);

        } catch (\Exception $exception) {

        }

        try {
            $this->templateProcessor->cloneRow('mes', 12);

            self::writeMonthlyGenerate($project);

        } catch (\Exception $exception) {

        }

        try {
            $totalYears = count($project->getAccumulatedCash());

            $this->templateProcessor->cloneRow('ano', $totalYears);

            self::writeAccumulatedCash($project, $totalYears);

        } catch (\Exception $exception) {

        }

        $options = [
            'filename' => substr(md5(uniqid(rand(1,6))), 0, 8)  . '.docx',
            'root' => 'proposal',
            'type' => 'theme',
            'access' => 'private'
        ];

        $outputFile = $this->container->get('app_storage')->location($options);

        $this->templateProcessor->saveAs($outputFile);

        return $outputFile;
    }

    /**
     * @param $number
     * @return string
     */
    private static function formatCurrency($number)
    {
        return sprintf('%s%s', '', number_format($number, 2, ',', '.'));
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
            $this->templateProcessor->setValue('geracao#'.($i + 1), $project->getMonthlyProduction()[$i]);
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

    /**
     * @param $components
     * @return mixed
     */
    private function getFamilyComponents($components)
    {
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

        return $familiesOfComponents;
    }

    /**
     * @param $familiesOfComponents
     */
    private function replaceComponents($familiesOfComponents)
    {
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
    }
}

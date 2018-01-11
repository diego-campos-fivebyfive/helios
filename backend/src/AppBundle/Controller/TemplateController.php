<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Service\Order\ComponentExtractor;
use AppBundle\Service\Support\Project;
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
        //dump($components[0]['quantity']);die;

        $path = $this->container->get('kernel')->getRootDir();
        $file = $path . '/cache/template.docx';

        $templateProcessor = new TemplateProcessor($file);

        $templateProcessor->setValue('ProjetoNumero', $project->getNumber());
        $templateProcessor->setValue([
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

        echo '<pre>';
        print_r($familiesOfComponents); die;

/*        $componentCount = count($components);

        $templateProcessor->cloneRow('descricao', $componentCount);

        for ($i = 0; $i < $componentCount; $i++) {
            $templateProcessor->setValue('descricao#' . ($i + 1), $components[$i]['description']);
            $templateProcessor->setValue('quantidade#' . ($i + 1), $components[$i]['quantity']);
        }

        $outputFile = $path . '/cache/test_docx.docx';

        $templateProcessor->saveAs($outputFile);

        dump($templateProcessor);die;*/
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
}

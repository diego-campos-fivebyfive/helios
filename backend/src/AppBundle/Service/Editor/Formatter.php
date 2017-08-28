<?php

namespace AppBundle\Service\Editor;

use AppBundle\Entity\Component\ProjectInterface;

class Formatter
{
    public static function format(ProjectInterface $project)
    {
        $metadata = $project->getMetadata();

        return [
            'groupGeneral' => [
                'label' => 'Dados Gerais',
                'value' => null,
                'handle' => 'label'
            ],
            'projectNumber' => [
                'label' => 'Número do Projeto',
                'value' => $project->getNumber(),
                'handle' => 'static'
            ],
            'power' => [
                'label' => 'Potência do Projeto',
                'value' => $project->getPower() . ' kWp',
                'handle' => 'static'
            ],
            'salePrice' => [
                'label' => 'Valor da Proposta',
                'value' => self::formatCurrency($project->getSalePrice()),
                'handle' => 'static'
            ],
            'customerName' => [
                'label' => 'Nome do Cliente',
                'value' => $project->getCustomer()->getName(),
                'handle' => 'static'
            ],
            'customerDocument' => [
                'label' => 'CPF/CNPJ',
                'value' => $project->getCustomer()->getDocument(),
                'handle' => 'static'
            ],
            'customerPhone' => [
                'label' => 'Telefone',
                'value' => $project->getCustomer()->getPhone(),
                'handle' => 'static'
            ],
            'customerEmail' => [
                'label' => 'E-mail',
                'value' => $project->getCustomer()->getEmail(),
                'handle' => 'static'
            ],
            // GENERATION
            'groupGeneration' => [
                'label' => 'Dados de Geração',
                'value' => null,
                'handle' => 'label'
            ],
            'annualGeneration' => [
                'label' => 'Geração Anual',
                'value' => round($metadata['total']['kwh_year']) . ' kWh',
                'handle' => 'static'
            ],
            'monthlyGeneration' => [
                'label' => 'Geração Média Mensal',
                'value' => round(($metadata['total']['kwh_year'] / 12)) . ' kWh',
                'handle' => 'static'
            ],
            'chartGeneration' => [
                'label' => 'Gráfico da Geração',
                'value' => $project->getMonthlyProduction(),
                'handle' => 'image'
            ],
            'chartFinancial' => [
                'label' => 'Caixa Acumulado',
                'value' => $project->getAccumulatedCash(),
                'handle' => 'image'
            ],
            // FINANCIAL
            'groupFinancial' => [
                'label' => 'Análise Financeira',
                'value' => null,
                'handle' => 'label'
            ],
            'lifetime' => [
                'label' => 'Tempo de Vida',
                'value' => $project->getLifetime() . ' anos',
                'handle' => 'static'
            ],
            'inflation' => [
                'label' => 'Inflação',
                'value' => $project->getInflation() . ' %',
                'handle' => 'static'
            ],
            'efficiencyLoss' => [
                'label' => 'Perda de eficiência',
                'value' => $project->getEfficiencyLoss().' %',
                'handle' => 'static'
            ],
            'annualCostOperation' => [
                'label' => 'Custo anual',
                'value' => self::formatCurrency($project->getAnnualCostOperation()),
                'handle' => 'static'
            ],
            'energyPrice' => [
                'label' => 'Preço do kWh + Impostos',
                'value' => self::formatCurrency($project->getEnergyPrice()),
                'handle' => 'static'
            ],
            'accumulatedCash' => [
                'label' => 'Acumulado',
                'value' => self::formatCurrency($project->getAccumulatedCash(true)),
                'handle' => 'static'
            ],
            'netPresentValue' => [
                'label' => 'VPL',
                'value' => self::formatCurrency($project->getNetPresentValue()),
                'handle' => 'static'
            ],
            'internalRateOfReturn' => [
                'label' => 'Taxa de retorno',
                'value' => $project->getInternalRateOfReturn().' %',
                'handle' => 'static'
            ],
            'paybackSimple' => [
                'label' => 'Payback Simples',
                'value' => self::formatPayback($project->getPaybackYears(), $project->getPaybackMonths()),
                'handle' => 'static'
            ],
            'paybackDiscounted' => [
                'label' => 'Payback Descontado',
                'value' => self::formatPayback($project->getPaybackYearsDisc(), $project->getPaybackMonthsDisc()),
                'handle' => 'static'
            ]
        ];
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
<?php

namespace AppBundle\Service\Support\Project;
use AppBundle\Entity\Financial\ProjectFinancialInterface;

/**
 * Interface ProjectAnalyzerInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface FinancialAnalyzerInterface
{
    /**
     * @return FinancialAnalyzerInterface
     */
    public static function analyze(ProjectFinancialInterface &$projectFinancial);
}
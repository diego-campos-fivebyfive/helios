<?php

namespace AppBundle\Service\Support\Project;

use AppBundle\Entity\Component\ProjectInterface;

/**
 * Interface ProjectAnalyzerInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface FinancialAnalyzerInterface
{
    /**
     * @return FinancialAnalyzerInterface
     */
    public static function analyze(ProjectInterface $project);
}
<?php

namespace AppBundle\Util\ProjectPricing;

use AppBundle\Entity\Component\ProjectInterface;

class CostPrice
{
    public function calculate(ProjectInterface $project)
    {
        $taxPercent = $project->getTaxPercent();
        foreach ($project->getProjectInverters() as $projectInverter){

            $pricingRange = $projectInverter->getMarkup();

            //$markup = $pricingRange->getMarkup();
            //custo * (1 + markup) / (1 - impostos)
            //$costPrice = $pricingRange->getPrice()
        }
    }
}
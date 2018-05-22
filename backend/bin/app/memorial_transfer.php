<?php

require_once(dirname(__FILE__) . '/config/functions.php');
getAutoload();

/**
 * Este script servirá para transferir os dados do antigo memorial para o novo modelo.
 */

connectDatabase();

$sourceMemorialId = 301;
$targetMemorialId = 20;

$levelsArray = \AppBundle\Entity\Precifier\Memorial::getDefaultLevels(true);

$levels = '(';
$c = 0;

foreach ($levelsArray as $level) {
    $c++;
    $levels .= "'".$level."'";
    if (count($levelsArray) != $c) {
        $levels .= ', ';
    }
}

$levels .= ')';

$sqlFamilies = "SELECT DISTINCT r.family as families 
                 FROM app_precifier_range r
                 WHERE r.memorial_id = {$targetMemorialId}";

$families = R::getCol($sqlFamilies);

$countComponents = 0;
$countNormalized = 0;

foreach ($families as $family) {

    $sqlComponents = "SELECT c.id, c.code FROM app_component_" . $family . " c";

    $components = R::getAll($sqlComponents);

    foreach ($components as $component) {

        $countComponents++;

        $componentId = $component['id'];
        $componentCode = $component['code'];

        $sqlRangePrecifier = "SELECT r.metadata 
                              FROM app_precifier_range r 
                              WHERE r.family = '{$family}' 
                              AND r.component_id = '{$componentId}'";

        $range = R::getRow($sqlRangePrecifier);

        $metadata = json_decode($range['metadata'], true);

        $sqlRangePricing =
            "SELECT r.initialPower as powerRange, r.level, r.cost_price, r.price, r.markup 
              FROM app_pricing_range r 
              WHERE r.memorial_id = {$sourceMemorialId} 
              AND r.code = '{$componentCode}' 
              AND r.level IN {$levels}";

        $baseRanges = R::getAll($sqlRangePricing);

        $costPrice = null;

        foreach ($baseRanges as $baseRange) {
            if (is_null($costPrice)) {
                $costPrice = (float) $baseRange['cost_price'];
            }

            $baseLevel = $baseRange['level'];
            $basePowerRange = $baseRange['powerRange'];
            $basePrice = $baseRange['price'];
            $baseMarkup = $baseRange['markup'];

            $metadata[$baseLevel][$basePowerRange]['price'] = (float) $basePrice;
            $metadata[$baseLevel][$basePowerRange]['markup'] = (float) $baseMarkup;
        }

        if ($baseRanges) {
            $metadata = json_encode($metadata);

            $sqlUpdate =
                "UPDATE app_precifier_range 
              SET cost_price = {$costPrice}, metadata = '{$metadata}'
              WHERE component_id = {$componentId} AND family = '{$family}'";

            $countNormalized++;

            R::exec($sqlUpdate);
        }

        $costPrice = null;
    }
}

print_r("Number of components processed: ".$countComponents."\n");
print_r("Number of ranges normalized: ".$countNormalized."\n");
print_r('finish'."\n");

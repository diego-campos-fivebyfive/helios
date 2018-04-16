<?php

namespace App\Generator\Structure;

class Ground {

    /**
     * @param $windSpeed
     * @param $moduleQuantity
     * @return array
     */
    public static function autoModuleQuantityPerTable($windSpeed, $moduleQuantity)
    {

        $maxModuleTableQuantity = 248;

        $minModuleTableQuantity = 8;
        if ($windSpeed >= 30 and $windSpeed < 50){
            $minModuleTableQuantity = 6;
        }

        $tables = array();

        $actualModuleQuantity = $moduleQuantity;
        $countTables = 0;
        for ($i = $moduleQuantity; $i > 0; $i -= $maxModuleTableQuantity) {

            if ($actualModuleQuantity >= $maxModuleTableQuantity) {
                $tables[$countTables] = $maxModuleTableQuantity;
                $actualModuleQuantity -= $maxModuleTableQuantity;
            } else {
                if ($actualModuleQuantity < $minModuleTableQuantity) {
                    if ($moduleQuantity >= $minModuleTableQuantity) {
                        $tables[$countTables - 1] += $actualModuleQuantity;
                    }
                } else {
                    $tables[$countTables] = $actualModuleQuantity;
                }
            }

            $countTables += 1;

        }

        return $tables;

    }

    /**
     * @param $windSpeed
     * @param $moduleQuantityPerTable
     * @return array
     */
    public static function allTablesMaterials($windSpeed, $moduleQuantityPerTable)
    {

        $countTables = count($moduleQuantityPerTable);
        $table = array();

        for ($i = 0; $i < $countTables; $i++) {
            $table[$i] = self::oneTableMaterials($windSpeed, $moduleQuantityPerTable[$i]);
        }

        return $table;

    }

    /**
     * @param $allTablesMaterials
     * @return array
     */
    public static function mergeTablesMaterials($allTablesMaterials)
    {

        $countAllTables = count($allTablesMaterials);
        $allMaterials = array();

        $notMergeMaterials = array(
            "balanceCrossSize",
            "mainCrossSize",
            "diagonalGapSize"
        );

        for ($i = 0; $i < $countAllTables; $i++) {
            $keys = array_keys($allTablesMaterials[$i]);
            $countKeys = count($keys);
            for ($k = 0; $k < $countKeys; $k++) {
                $actualKey = $keys[$k];
                if (!array_key_exists($actualKey , $allMaterials) or is_numeric(array_search($actualKey, $notMergeMaterials))) {
                    $allMaterials[$actualKey] = (int) $allTablesMaterials[$i][$actualKey];
                }else{
                    $allMaterials[$actualKey] += (int) $allTablesMaterials[$i][$actualKey];
                }
            }
        }

        return $allMaterials;

    }

    /**
     * @param $windSpeed
     * @param $moduleQuantity
     * @return array
     */
    private static function oneTableMaterials($windSpeed, $moduleQuantity)
    {

        $porticoBalanceQuantity = self::porticoBalanceQuantity($windSpeed, $moduleQuantity);
        $crossQuantity = self::crossQuantity($porticoBalanceQuantity["gapSize"], $porticoBalanceQuantity["tableSize"], $porticoBalanceQuantity["balanceQuantity"]);
        $clampsQuantity = self::clampsQuantity($moduleQuantity);
        $diagonalQuantity = self::diagonalQuantity($porticoBalanceQuantity["tableSize"], $porticoBalanceQuantity["gapSize"]);
        $diagonalUnionQuantity = self::diagonalUnionQuantity($diagonalQuantity["diagonalQuantity"]);
        $screwQuantity = self::screwQuantity($porticoBalanceQuantity["porticoQuantity"], $diagonalQuantity["diagonalQuantity"]);

        $materials = array(
            "porticoQuantity" => $porticoBalanceQuantity["porticoQuantity"],
            "clampsQuantity" => $clampsQuantity,
            "screwQuantity" => $screwQuantity,
            "diagonalUnionQuantity" => $diagonalUnionQuantity
        );

        $materials = array_merge($materials, $diagonalQuantity, $crossQuantity);

        return $materials;

    }

    /**
     * @param $windSpeed
     * @param $moduleQuantity
     * @return array
     */
    private static function porticoBalanceQuantity($windSpeed, $moduleQuantity)
    {

        $gapSize = 4;
        if ($windSpeed >= 50){
            $gapSize = 2;
        }elseif ($windSpeed >= 40) {
            $gapSize = 3;
        }

        $tableSize = ceil($moduleQuantity / 2);
        $remainder = $tableSize % $gapSize;

        if ($remainder >= 3) {
            $tableSize += 1;
            $remainder = $tableSize % $gapSize;
        }

        $porticoQuantity = floor($tableSize / $gapSize) + 1;
        if ($gapSize == 2) {
            $porticoQuantity = (floor($tableSize / 4) * 2) + 1;
        }
        $balanceQuantity = $remainder;

        $porticoBalanceQuantity = array(
            "tableSize" => $tableSize,
            "gapSize" => $gapSize,
            "porticoQuantity" => $porticoQuantity,
            "balanceQuantity" => $balanceQuantity
        );

        return $porticoBalanceQuantity;

    }

    /**
     * @param $gapSize
     * @param $tableSize
     * @param $balanceQuantity
     * @return mixed
     */
    private static function crossQuantity($gapSize, $tableSize, $balanceQuantity)
    {

        $mainCrossSize = $gapSize;
        if ($gapSize == 2){
            $mainCrossSize = 4;
        }

        $totalMainCrossSize = $tableSize;

        if ($balanceQuantity > 0) {
            $balanceCrossSize = $mainCrossSize + 1;
            $totalBalanceCrossSize = $balanceCrossSize * $balanceQuantity;
            $totalMainCrossSize = $tableSize - $totalBalanceCrossSize;

            $totalCross["balanceCrossQuantity"] = 4 * $balanceQuantity;
            $totalCross["balanceCrossSize"] = $balanceCrossSize;

        }

        $mainCrossQuantity = $totalMainCrossSize / $mainCrossSize;

        $totalCross["mainCrossQuantity"] = 4 * $mainCrossQuantity;
        $totalCross["mainCrossSize"] = $mainCrossSize;

        return $totalCross;

    }

    private static function clampsQuantity($moduleQuantity)
    {

        $clampsQuantity = ceil($moduleQuantity * 4 * 1.01) + 4;

        return $clampsQuantity;

    }

    /**
     * @param $tableSize
     * @param $gapSize
     * @return array
     */
    private static function diagonalQuantity($tableSize, $gapSize)
    {

        $diagonalQuantity = ceil($tableSize / 20);

        $totalDiagonal = array(
            "diagonalQuantity" => $diagonalQuantity,
            "diagonalGapSize" => $gapSize
        );
        return $totalDiagonal;

    }

    /**
     * @param $diagonalQuantity
     * @return float|int
     */
    private static function diagonalUnionQuantity($diagonalQuantity)
    {

        $diagonalUnionQuantity = $diagonalQuantity * 2;

        return $diagonalUnionQuantity;

    }

    /**
     * @param $porticoQuantity
     * @param $diagonalQuantity
     * @return float
     */
    private static function screwQuantity($porticoQuantity, $diagonalQuantity)
    {

        $screwQuantity = ceil(((11 * $porticoQuantity) + (4 * $diagonalQuantity)) * 1.01);

        return $screwQuantity;

    }

}

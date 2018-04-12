<?php
/**
 * Created by PhpStorm.
 * User: mauroandre
 * Date: 05/04/18
 * Time: 10:08
 */
/**
 * TIPOS DE MATERIAIS
 * Type
 * -------------------------
 * ground_portico -> Portico
 * ground_cross -> Terça (sizes: 3, 4 e 5)
 * ground_clamps -> Clamps
 * ground_diagonal -> Diagonal (sizes: 2, 3 e 4)
 * ground_screw -> Parafuso
 */

function porticoBalanceQuantity($windSpeed, $moduleQuantity)
{

    $gapSize = 3;
    if ($windSpeed == 30) {
        $gapSize = 4;
    } elseif ($windSpeed == 50) {
        $gapSize = 2;
    }

    $tableSize = ceil($moduleQuantity / 2);
    $remainder = $tableSize % $gapSize;

    if ($remainder >= 3) {
        $tableSize += 1;
        $remainder = $tableSize % $gapSize;
    }

    $porticoQuantity = floor($tableSize / $gapSize) + 1;
    if ($windSpeed == 50) {
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

function crossQuantity($windSpeed, $tableSize, $balanceQuantity)
{

    $mainCrossSize = 4;
    if ($windSpeed == 40) {
        $mainCrossSize = 3;
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

function clampsQuantity($moduleQuantity)
{

    $clampsQuantity = ceil($moduleQuantity * 4 * 1.01) + 4;

    return $clampsQuantity;

}

function diagonalQuantity($tableSize, $gapSize)
{

    $diagonalQuantity = ceil($tableSize / 20);

    $totalDiagonal = array(
        "diagonalQuantity" => $diagonalQuantity,
        "diagonalGapSize" => $gapSize
    );
    return $totalDiagonal;

}

function screwQuantity($porticoQuantity, $diagonalQuantity)
{
    $screwQuantity = ceil(((11 * $porticoQuantity) + (4 * $diagonalQuantity)) * 1.01);

    return $screwQuantity;
}

/**
 * Determina os materiais de uma mesa
 *
 * @param $windSpeed
 * @param $moduleQuantity
 * @return array
 */
function oneTableMaterials($windSpeed, $moduleQuantity)
{
    $porticoBalanceQuantity = porticoBalanceQuantity($windSpeed, $moduleQuantity);
    $crossQuantity = crossQuantity($windSpeed, $porticoBalanceQuantity["tableSize"], $porticoBalanceQuantity["balanceQuantity"]);
    $clampsQuantity = clampsQuantity($moduleQuantity);
    $diagonalQuantity = diagonalQuantity($porticoBalanceQuantity["tableSize"], $porticoBalanceQuantity["gapSize"]);
    $screwQuantity = screwQuantity($porticoBalanceQuantity["porticoQuantity"], $diagonalQuantity["diagonalQuantity"]);

    $materials = array(
        "porticoQuantity" => $porticoBalanceQuantity["porticoQuantity"],
        "clampsQuantity" => $clampsQuantity,
        "screwQuantity" => $screwQuantity
    );

    $materials = array_merge($materials, $crossQuantity, $diagonalQuantity);

    return $materials;
}

//----------------------------------------------------------------------

/**
 * Define a quantidade de mesas e o número de módulos por mesa
 *
 * @param $windSpeed
 * @param $moduleQuantity
 * @return array
 */
function autoModuleQuantityPerTable($windSpeed, $moduleQuantity)
{
    $maxModuleTableQuantity = 248;
    $minModuleTableQuantity = 8;
    if ($windSpeed == 40) {
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
 * Determina os materiais de todas as mesas
 *
 * @param $windSpeed
 * @param $moduleQuantityPerTable
 * @return array
 */
function allTablesMaterials($windSpeed, $moduleQuantityPerTable)
{
    $countTables = count($moduleQuantityPerTable);
    $table = array();

    for ($i = 0; $i < $countTables; $i++) {
        $table[$i] = oneTableMaterials($windSpeed, $moduleQuantityPerTable[$i]);
    }

    return $table;
}

/**
 * Agrupa com somatória as quantidades de materias de todas as mesas
 * Exceto balanceCrossSize e mainCrossSize, que são medidas e não quantidades
 *
 * @param $allTablesMaterials
 * @return array
 */
function mergeTablesMaterials($allTablesMaterials)
{
    $countAllTables = count($allTablesMaterials);
    $allMaterials = array();

    $notMergeMaterials = array(
        "balanceCrossSize",
        "diagonalGapSize",
        "mainCrossSize"
    );

    for ($i = 0; $i < $countAllTables; $i++) {
        $keys = array_keys($allTablesMaterials[$i]);
        $countKeys = count($keys);
        for ($k = 0; $k < $countKeys; $k++) {
            $actualKey = $keys[$k];
            if (!array_key_exists($actualKey , $allMaterials) or is_numeric(array_search($actualKey, $notMergeMaterials))) {
                $allMaterials[$actualKey] = $allTablesMaterials[$i][$actualKey];
            }else{
                $allMaterials[$actualKey] += $allTablesMaterials[$i][$actualKey];
            }
        }
    }

    return $allMaterials;

}

$windSpeed = 35;        // Velocidade do vento (ISOPLETA)
$moduleQuantity = 8;    // Módulos do sistema

echo "wind speed = $windSpeed / module quantity = $moduleQuantity <br><br>";

$autoModuleQuantityPerTable = autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
$allTablesMaterials = allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
$mergeTablesMaterials = mergeTablesMaterials($allTablesMaterials);

echo "<br>";
print_r($autoModuleQuantityPerTable);
echo "<br><br>";
for ($i = 0; $i < count($allTablesMaterials); $i++) {
    print_r($allTablesMaterials[$i]);
    echo "<br>";
}
echo "<br><br>";
print_r($mergeTablesMaterials);


?>

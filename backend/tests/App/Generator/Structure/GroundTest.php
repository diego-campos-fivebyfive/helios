<?php

namespace Tests\App\Generator\Structure;

use App\Generator\Structure\Ground as Ground;
use Tests\App\Generator\GeneratorTest;

/**
 * Class HelperTest
 * ESPERAR CORREÇÃO MAURO PARA FAZER ASSERT DE EXPECTED RESULT
 * @group generator_structure_ground
 */
class GroundTest extends GeneratorTest
{
    public function testResolve()
    {
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 6;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 29,
            'screwQuantity' => 27,
            'mainCrossQuantity' => 4,
            'mainCrossSize' => 3,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 3
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(6, $quantity);
        }
        self::assertEquals(count($allTablesMaterials), 1);
        //self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            self::assertGreaterThanOrEqual(0, $value);
        }

        //-------------------------------------------------------------------------------//
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 8;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        // ESPERAR CORREÇÃO MAURO
        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 37,
            'screwQuantity' => 27,
            'balanceCrossQuantity' => 4,
            'balanceCrossSize' => 4,
            'mainCrossQuantity' => 0,
            'mainCrossSize' => 3,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 3
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(8, $quantity);
        }
        self::assertEquals(count($allTablesMaterials), 1);
        //self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            self::assertGreaterThanOrEqual(0, $value);
        }

        //-------------------------------------------------------------------------------//
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 4;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        self::assertEmpty($autoModuleQuantityPerTable);
        self::assertEmpty($allTablesMaterials);
        self::assertEmpty($mergeTablesMaterials);

        //-------------------------------------------------------------------------------//
        $windSpeed = 35;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 6;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        // ESPERAR CORREÇÃO MAURO
        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 37,
            'screwQuantity' => 27,
            'balanceCrossQuantity' => 4,
            'balanceCrossSize' => 4,
            'mainCrossQuantity' => 0,
            'mainCrossSize' => 3,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 3
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(6, $quantity);
        }
        self::assertEquals(count($allTablesMaterials), 1);
        //self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            self::assertGreaterThanOrEqual(0, $value);
        }

        //-------------------------------------------------------------------------------//
        $windSpeed = 35;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 8;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 37,
            'screwQuantity' => 27,
            'diagonalUnionQuantity' => 2,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 4,
            'mainCrossQuantity' => 4,
            'mainCrossSize' => 4
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(8, $quantity);
        }
        self::assertEquals(count($allTablesMaterials), 1);
        //self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            self::assertGreaterThanOrEqual(0, $value);
        }

        //-------------------------------------------------------------------------------//
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 9;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 37,
            'screwQuantity' => 27,
            'balanceCrossQuantity' => 4,
            'balanceCrossSize' => 4,
            'mainCrossQuantity' => 0,
            'mainCrossSize' => 3,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 3
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(9, $quantity);
        }
        self::assertEquals(count($allTablesMaterials), 1);
        //self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                //self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            //self::assertGreaterThanOrEqual(0, $value);
        }

        //-------------------------------------------------------------------------------//
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 496;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        $expectedResults = [
            'porticoQuantity' => 2,
            'clampsQuantity' => 37,
            'screwQuantity' => 27,
            'balanceCrossQuantity' => 4,
            'balanceCrossSize' => 4,
            'mainCrossQuantity' => 0,
            'mainCrossSize' => 3,
            'diagonalQuantity' => 1,
            'diagonalGapSize' => 3
        ];

        foreach ($autoModuleQuantityPerTable as $quantity) {
            self::assertEquals(248, $quantity);
        }

        self::assertEquals(count($allTablesMaterials), 2);
//        self::assertEquals($mergeTablesMaterials, $expectedResults);
        foreach ($allTablesMaterials as $table) {
            foreach ($table as $key => $value) {
                self::assertGreaterThanOrEqual(0, $value);
            }
        }
        foreach ($mergeTablesMaterials as $value) {
            self::assertGreaterThanOrEqual(0, $value);
        }
    }

}

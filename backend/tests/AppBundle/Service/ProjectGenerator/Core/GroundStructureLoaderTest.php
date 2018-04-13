<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Component\Maker;
use AppBundle\Service\ProjectGenerator\Core\GroundStructureLoader;
use App\Generator\Structure\Ground;
use AppBundle\Service\ProjectGenerator\ProjectGenerator;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Entity\DataFixtures\Component\ModuleData;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group core_ground_structure_loader
 */
class GroundStructureLoaderTest extends AppTestCase
{

    public function testLoad()
    {
        $structureManager = $this->manager('structure');

        $makerManager = $this->manager('maker');

        $makers = $makerManager->findAll();
        $structures = $structureManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($structures as $structure1) {
            $structureManager->delete($structure1);
        }

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('structure');
        $makerManager->save($maker);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('structure');
        $makerManager->save($maker2);

        /** @var Structure $structure */
        $structure = $structureManager->create();
        $structure->setType('ground_portico');
        $structure->setGeneratorLevels(["black","platinum"]);
        $structure->setDescription('Teste');
        $structure->setCode(111);
        $structure->setMaker($maker);
        $structureManager->save($structure);

        /** @var Structure $structure2 */
        $structure2 = $structureManager->create();
        $structure2->setType('ground_clamps');
        $structure2->setGeneratorLevels(["black"]);
        $structure2->setDescription('Teste');
        $structure2->setCode(222);
        $structure2->setMaker($maker);
        $structureManager->save($structure2);

        /** @var Structure $structure10 */
        $structure10 = $structureManager->create();
        $structure10->setType('ground_screw');
        $structure10->setGeneratorLevels(["black"]);
        $structure10->setDescription('Teste');
        $structure10->setCode(1010);
        $structure10->setMaker($maker);
        $structureManager->save($structure10);

        /** @var Structure $structure2 */
        $structure11 = $structureManager->create();
        $structure11->setType('ground_diagonal_union');
        $structure11->setGeneratorLevels(["black"]);
        $structure11->setDescription('Teste');
        $structure11->setCode(1111);
        $structure11->setMaker($maker);
        $structureManager->save($structure11);

        /** @var Structure $structure3 */
        $structure3 = $structureManager->create();
        $structure3->setType('ground_diagonal');
        $structure3->setSize(2);
        $structure3->setGeneratorLevels(["black","platinum"]);
        $structure3->setDescription('Teste');
        $structure3->setCode(333);
        $structure3->setMaker($maker2);
        $structureManager->save($structure3);

        /** @var Structure $structure4 */
        $structure4 = $structureManager->create();
        $structure4->setType('ground_diagonal');
        $structure4->setSize(3);
        $structure4->setGeneratorLevels(["black","platinum"]);
        $structure4->setDescription('Teste');
        $structure4->setCode(444);
        $structure4->setMaker($maker2);
        $structureManager->save($structure4);

        /** @var Structure $structure5 */
        $structure5 = $structureManager->create();
        $structure5->setType('ground_diagonal');
        $structure5->setSize(4);
        $structure5->setGeneratorLevels(["black","platinum"]);
        $structure5->setDescription('Teste');
        $structure5->setCode(555);
        $structure5->setMaker($maker2);
        $structureManager->save($structure5);

        /** @var Structure $structure6 */
        $structure6 = $structureManager->create();
        $structure6->setType('ground_cross');
        $structure6->setSize(3);
        $structure6->setGeneratorLevels(["black"]);
        $structure6->setDescription('Teste');
        $structure6->setCode(666);
        $structure6->setMaker($maker2);
        $structureManager->save($structure6);

        /** @var Structure $structure7 */
        $structure7 = $structureManager->create();
        $structure7->setType('ground_cross');
        $structure7->setSize(4);
        $structure7->setGeneratorLevels(["black"]);
        $structure7->setDescription('Teste');
        $structure7->setCode(777);
        $structure7->setMaker($maker2);
        $structureManager->save($structure7);

        /** @var Structure $structure8 */
        $structure8 = $structureManager->create();
        $structure8->setType('ground_cross');
        $structure8->setSize(5);
        $structure8->setGeneratorLevels(["black"]);
        $structure8->setDescription('Teste');
        $structure8->setCode(777);
        $structure8->setMaker($maker2);
        $structureManager->save($structure8);

        $structureLoader = new GroundStructureLoader([
            'manager' => $structureManager,
            'maker' => $maker->getId()
        ]);

        //------------------------------------------------------------------------------------------------
        $windSpeed = 40;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 2480;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        //dump($mergeTablesMaterials);die;

        $results = $structureLoader->load($mergeTablesMaterials);
        $expectedTypes = ['ground_portico', 'ground_cross', 'ground_clamps', 'ground_diagonal', 'ground_diagonal_union', 'ground_screw'];

        self::assertEquals(7, count($results));
        foreach ($results as $result) {
            self::assertTrue(in_array($result->getType(), $expectedTypes));
        }

        //------------------------------------------------------------------------------------------------
        $windSpeed = 35;        // Velocidade do vento (ISOPLETA)
        $moduleQuantity = 8;  // Módulos do sistema

        $autoModuleQuantityPerTable = Ground::autoModuleQuantityPerTable($windSpeed, $moduleQuantity);
        $allTablesMaterials = Ground::allTablesMaterials($windSpeed, $autoModuleQuantityPerTable);
        $mergeTablesMaterials = Ground::mergeTablesMaterials($allTablesMaterials);

        //dump($mergeTablesMaterials);die;

        $results = $structureLoader->load($mergeTablesMaterials);
        $expectedTypes = ['ground_portico', 'ground_cross', 'ground_clamps', 'ground_diagonal', 'ground_diagonal_union', 'ground_screw'];

        self::assertEquals(6, count($results));
        foreach ($results as $result) {
            self::assertTrue(in_array($result->getType(), $expectedTypes));
        }
    }
}

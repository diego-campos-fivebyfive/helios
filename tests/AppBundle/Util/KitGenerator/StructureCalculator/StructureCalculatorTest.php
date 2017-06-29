<?php

namespace Tests\AppBundle\Util\KitGenerator\StructureCalculator;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\StructureCalculator\Item;
use AppBundle\Util\KitGenerator\StructureCalculator\Module;
use AppBundle\Util\KitGenerator\StructureCalculator\Profile;
use AppBundle\Util\KitGenerator\StructureCalculator\Structure;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class StructureCalculatorTest
 * @group structure_calculator
 */
class StructureCalculatorTest extends WebTestCase
{
    use ObjectHelperTest;

    public function testModule()
    {
        $data = [
            'cellNumber' => 60,
            'position' => Module::VERTICAL,
            'length' =>  1.65,
            'width' => .992,
            'quantity' => 140
        ];

        $module = new Module();

        $this->fluentSettersTest($module, $data);

        $groups = array_fill(0, 8, 18);
        $groups[count($groups)-1] = 14;

        $this->assertEquals($groups, $module->getGroups());

        $structure = new Structure();

        $structure->setModule($module);

        $profiles = $this->createProfiles();
        foreach($profiles as $profile){
            $structure->addProfile($profile);
        }

        $structure->setRoofType(Structure::ROOF_ROMAN_AMERICAN);

        // Base
        $base = new Item();
        $base
            ->setId(41)
            ->setType(Item::TYPE_BASE)
            ->setSubtype(Item::BASE_HOOK)
        ;

        // Terminal Final
        $terminalFinal = new Item();
        $terminalFinal
            ->setId(52)
            ->setType(Item::TYPE_TERMINAL)
            ->setSubtype(Item::TERMINAL_FINAL)
            ->setSize(.035)
        ;

        // Terminal Intermediário
        $terminalMiddle = new Item();
        $terminalMiddle
            ->setId(53)
            ->setType(Item::TYPE_TERMINAL)
            ->setSubtype(Item::TERMINAL_MIDDLE)
            ->setSize(.012)
        ;

        // Junção
        $junction = new Item();
        $junction
            ->setId(40)
            ->setType(Item::TYPE_JUNCTION)
            ->setDescription('K2 System_JUNÇÃO PARA PERFIL EM ALKUMINIO K2 - IMPORTADO')
        ;

        $structure
            ->addItem($base)
            ->addItem($terminalFinal)
            ->addItem($terminalMiddle)
            ->addItem($junction)
        ;

        $structure->calculate();
    }

    /**
     * @expectedException \Exception
     */
    public function testComponentExceptions()
    {
        //$this->markTestSkipped('Skip Test');

        $itemA = new Item();

        $itemA
            ->setId(12)
            ->setQuantity(2)
            ->setType(Item::TYPE_TERMINAL);

        $itemB = new Item();
        $itemB
            ->setId(34)
            ->setType(Item::TYPE_TERMINAL);

        $structure = new Structure();

        $structure
            ->addItem($itemA)
            ->addItem($itemB)
        ;
    }

    public function testCalculations()
    {
        $this->markTestSkipped('Skip Test');

        $profilesData = json_decode('[{"id":"27","codigo":"K201001929","descricao":"K2 System_PERFIL ALUMINIO ULTRALIGHT 6,1MT - IMPORTADO","maker":"2","tipo":"perfil","subtipo":"roman","tamanho":"6.1"},{"id":"28","codigo":"K2010019294MT","descricao":"K2 System_PERFIL ALUMINIO ULTRALIGHT 4,07MT - IMPORTADO","maker":"2","tipo":"perfil","subtipo":"roman","tamanho":"4.07"},{"id":"29","codigo":"K2010019293MT","descricao":"K2 System_PERFIL ALUMINIO ULTRALIGHT 3,05MT - IMPORTADO","maker":"2","tipo":"perfil","subtipo":"roman","tamanho":"3.05"},{"id":"30","codigo":"K2010019292MT","descricao":"K2 System_PERFIL ALUMINIO ULTRALIGHT 2,03MT - IMPORTADO","maker":"2","tipo":"perfil","subtipo":"roman","tamanho":"2.03"},{"id":"31","codigo":"K20100192915MT","descricao":"K2 System_PERFIL ALUMINIO ULTRALIGHT 1,5MT - IMPORTADO","maker":"2","tipo":"perfil","subtipo":"roman","tamanho":"1.525"}]');

        # profiles
        $items = [];
        foreach ($profilesData as $profileData){
            $item = new Item();
            $item
                ->setType(Item::TYPE_PROFILE)
                ->setSize($profileData->tamanho)
                ->setId($profileData->id)
                ->setDescription($profileData->descricao)
                ->setSubtype('profile_'.$profileData->subtipo)
            ;

            $this->assertTrue($item->isValid());

            $items[] = $item;
        }

        # module
        $module = new Item();

        $module
            ->setId(1345)
            ->setType(Item::TYPE_MODULE)
            ->setCellNumber(60)
            ->setLength(1.65)
            ->setWidth(0.992)
            ->setQuantity(84)
        ;

        # terminalFinal
        $terminalFinal = new Item();
        $terminalFinal
            ->setId(123)
            ->setType(Item::TYPE_TERMINAL)
            ->setSubtype(Item::TERMINAL_FINAL)
            ->setSize(0.035)
        ;

        

        $items[] = $module;

        $structure = new Structure();

        foreach ($items as $item){
            $structure->addItem($item);
        }

        $structure->calculate();

        return;

        $profiles = [];

        $itemA = new Item();
        $itemA
            ->setType(Item::TYPE_PROFILE)
            ;

        return;

        $module = new Module();

        $module
            ->setCellNumber(60)
            ->setLength(1.65)
            ->setWidth(0.992)
        ;

        $structure = new Structure();

        $structure->setProfiles($profiles);
        //$structure->module = $module;
        $structure->endTerminalWidth = 0.035;
        $structure->inTerminalWith = 0.012;

        $structure
            ->setRoofType(Structure::ROOF_ROMAN_AMERICAN)
            ->setPosition(Structure::POSITION_VERTICAL)
            ->setTotalModules(140)
        ;

        $this->assertEquals(0, $structure->getMaxProfileSize());
        $this->assertEquals(0.992, $structure->getDimension());
        $this->assertEquals(18, $structure->getModulesPerLine());
        $this->assertTrue($structure->isVertical());

        $structure->calculate();
    }

    private function createProfiles()
    {
        $data = [
            [
                'size' => 6.1
            ],
            [
                'size' => 4.07
            ],
            [
                'size' => 3.05
            ],
            [
                'size' => 2.03
            ],
            [
                'size' => 1.525
            ],
            /*[
                'size' => 6.1
            ],
            [
                'size' => 4.07
            ],
            [
                'size' => 3.05
            ],
            [
                'size' => 2.03
            ],
            [
                'size' => 1.525
            ]*/
        ];

        $profiles = [];
        foreach ($data as $config){
            $profile = new Profile();
            $this->fluentSettersTest($profile, $config);
            $profiles[] = $profile;
        }

        return $profiles;
    }
}
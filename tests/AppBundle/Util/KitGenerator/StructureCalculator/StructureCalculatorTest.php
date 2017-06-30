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

    public function testWithRoofType()
    {
        $module =  $this->createModule();
        $profiles = $this->createProfiles();

        $structure = new Structure();

        $structure->setModule($module);

        foreach($profiles as $profile){
            $structure->addProfile($profile);
        }

        $structure->setRoofType(Structure::ROOF_FIBERGLASS);

        // TELHADO ROMANO/AMERICANO
        // Base Gancho
        // $base = $this->createBaseHook();
        // Terminal Final
        $terminalFinal = $this->createTerminalFinal();
        // Terminal Intermediário
        $terminalMiddle = $this->createTerminalMiddle();
        // Junção
        $junction = $this->createJunction();
        // Parafuso Martelo
        $fixerScrew = $this->createFixerScrew();
        // Porca M10
        $fixerNut = $this->createFixerNut();

        // TELHADO FIBROCIMENTO
        $base = $this->createBaseScrewStructural();
        $baseTriangleVertical = $this->createBaseTriangleVertical();

        // Add items
        $structure
            ->addItem($base)
            ->addItem($terminalFinal)
            ->addItem($terminalMiddle)
            ->addItem($junction)
            ->addItem($fixerScrew)
            ->addItem($fixerNut)
        ;

        $data = $structure->calculate();

        var_dump($structure->getItems()); die;

        $this->assertEquals(140, $structure->getModule()->getQuantity());
        //$this->assertEquals();
    }

    private function createProfiles()
    {
        $data = [
            [
                'id' => 'SC004SSRR6MT',
                'description' => 'SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 6,3MT',
                'size' => 6.3
            ],
            [
                'id' => 'SSRR4MT',
                'description' => 'ICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 4,2MT',
                'size' => 4.2
            ],

            [
                'id' => 'SSRR3MT',
                'description' => 'SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 3,15MT',
                'size' => 3.15
            ],
            [
                'id' => 'SSRR2MT',
                'description' => 'SICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 2,10MT ',
                'size' => 2.1
            ],
            [
                'id' => 'SSRR1MT',
                'description' => 'ICES SOLAR PERFIL ALUMINIO ROMAN ROOFTOP 1,57 MT ',
                'size' => 1.575
            ]
        ];

        $profiles = [];
        foreach ($data as $config){
            $profile = new Profile();
            $this->fluentSettersTest($profile, $config);
            $profiles[] = $profile;
        }

        return $profiles;
    }

    /**
     * @return Module
     */
    private function createModule()
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

        return $module;
    }

    /**
     * @return Item
     */
    private function createBaseHook()
    {
        $data = [
            'id' => 'GCHO2P',
            'description' => 'SICES SOLAR GANCHO AISI 316 - TELHAS REGULAÇÃO 2 PONTOS - NACIONAL',
            'type' => Item::TYPE_BASE,
            'subtype' => Item::BASE_HOOK
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createBaseScrewStructural()
    {
        $data = [
            'id' => 'SSP12X300',
            'description' => 'SICES SOLAR PARAFUSO ESTRUTURAL AISI 316M12X300 - NACIONAL',
            'type' => Item::TYPE_BASE,
            'subtype' => Item::BASE_SCREW_STRUCTURAL
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createJunction()
    {
        $data = [
            'id' => 11,
            'type' => Item::TYPE_JUNCTION,
            'description' => 'SICES SOLAR JUNÇÃO PARA PERFIL EM ALUMINIO - NACIONAL'
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createFixerScrew()
    {
        $data = [
            'id' => 'SSPCM28',
            'description' => 'SICES SOLAR PARAFUSO CABECA MARTELO M10 28/15',
            'type' => Item::TYPE_FIXER,
            'subtype' => Item::FIXER_SCREW
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createFixerNut()
    {
        $data = [
            'id' => 'SSPM10',
            'description' => 'SICES SOLAR PORCA M10 INOX A2',
            'type' => Item::TYPE_FIXER,
            'subtype' => Item::FIXER_NUT
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createTerminalFinal()
    {
        $data = [
            'id' => 'SSTF',
            'description' => 'SICES SOLAR TERMINAL FINAL 39..41MM for CAN - NACIONAL',
            'type' => Item::TYPE_TERMINAL,
            'subtype' => Item::TERMINAL_FINAL,
            'size' => 0.035
        ];

        return $this->createItem($data);
    }

    /**
     * @return Item
     */
    private function createTerminalMiddle()
    {
        $data = [
            'id' => 'SSTI',
            'description' => 'SICES SOLAR TERMINAL INTERMEDIARIO 39..44MM for CAN/AVP - NACIONAL',
            'type' => Item::TYPE_TERMINAL,
            'subtype' => Item::TERMINAL_MIDDLE,
            'size' => 0.012
        ];

        return $this->createItem($data);
    }

    private function createBaseTriangleVertical()
    {
        $data = [
            'id' => 'SSTI',
            'description' => 'SICES SOLAR TERMINAL INTERMEDIARIO 39..44MM for CAN/AVP - NACIONAL',
            'type' => Item::TYPE_TERMINAL,
            'subtype' => Item::TERMINAL_MIDDLE,
            'size' => 0.012
        ];
    }

    /**
     * @param array $data
     * @return Item
     */
    private function createItem(array $data)
    {
        $item = new Item();

        $this->fluentSettersTest($item, $data);

        return $item;
    }
}
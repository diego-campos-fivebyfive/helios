<?php


namespace AppBundle\Util\KitGenerator\StructureCalculator;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollectionInterface;

//use AppBundle\Util\KitGenerator\InverterCombiner\ModuleInterface;
//use AppBundle\Util\KitGenerator\StructureCalculator\Component\ProfileInterface;

/**
 * Class Structure
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
class Structure implements StructureInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $roofType;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $modulesPerLine;

    /**
     * @var int
     */
    //private $totalModules;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var array
     */
    private $components;

    /**
     * @var array
     */
    private $items;


    private $combinedCollection;

    /**
     * @var array
     */
    private $profiles;

    /**
     * @var ModuleInterface
     */
    private $module;

    public $endTerminalWidth;

    public $inTerminalWith;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->modulesPerLine = 1;
        $this->position = self::POSITION_VERTICAL;
        $this->groups = [];
        $this->components = [];
        $this->profiles = [];
        $this->items = [];
    }

    /**
     * @inheritDoc
     */
    public function addProfile(ProfileInterface $profile)
    {
        $this->profiles[] = $profile;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProfiles(array $profiles)
    {
        $this->validateProfiles($profiles);

        $this->profiles = $profiles;

        return $this;
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @inheritDoc
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /** ---------------- */

    /**
     * @inheritDoc
     */
    public function setCombinedCollection(CombinedCollectionInterface $combinedCollection)
    {
        $this->combinedCollection = $combinedCollection;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCombinedCollection()
    {
        return $this->combinedCollection;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Structure
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRoofType()
    {
        return $this->roofType;
    }

    /**
     * @param int $roofType
     * @return Structure
     */
    public function setRoofType($roofType)
    {
        $this->roofType = $roofType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTotalModules($totalModules)
    {
        $this->totalModules = $totalModules;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalModules()
    {
        //return $this->totalModules;
    }

    /**
     * @inheritDoc
     */
    public function calculate()
    {
        $this->validate();

        /**
         * TODO: Validar existência do item [módulo]
         */
        /*$module           = $this->getItemByType(Item::TYPE_MODULE);
        $this->handleGroups($module);

        $dimension        = $this->isVertical() ? $module->getWidth() : $module->getLength() ;
        $maxProfileSize   = $this->getMaxProfileSize($module);
        $modulesPerLine   = $this->getModulesPerLine($module);
        $terminalFinal    = $this->getItemByType(Item::TERMINAL_FINAL);
        var_dump($terminalFinal); die;
        $endTerminalWidth = $this->endTerminalWidth;
        $inTerminalWidth  = $this->inTerminalWith;
        //$modulesPerLine   = $this->modulesPerLine;
        $countProfiles    = count($this->profiles);*/

        $module         = $this->module;
        $groups         = $module->getGroups();
        $dimension      = $module->getDimension();
        $maxProfileSize = $module->getMaxProfileSize();
        $modulesPerLine = $module->getModulesPerLine();
        $linesOfModules = 1;
        $countProfiles  = count($this->profiles);
        $base           = $this->getItemByType(Item::TYPE_BASE);
        $terminalFinal  = $this->getItemByType(Item::TERMINAL_FINAL);
        $terminalMiddle = $this->getItemByType(Item::TERMINAL_MIDDLE);

        $totalUsedProfiles  = array_fill(0, $countProfiles, 0);
        $totalJunction      = 0;
        $totalEndTerminal   = 0;
        $totalInTerminal    = 0;
        $totalProfileMiddlePlate = 0;
        $totalBase          = 0;
        $totalScrewHammer   = 0;
        $totalNutM10        = 0;
        $totalScrewStr      = 0;
        $totalScrewAuto     = 0;
        $totalSpeedClip     = 0;
        $totalTriangle      = 0;
        $totalPlate         = 0;

        for ($i = 0; $i < count($groups); $i++) {

            $quantityModules = $groups[$i];

            //$tam_linha = ($qte_mod * $dim_usada) + (($qte_mod - 1) * $ter_int_largura) + (2 * $ter_final_largura);
            $lineSize = ($quantityModules * $dimension) + (($quantityModules - 1) * $terminalMiddle->getSize()) + (2 * $terminalMiddle->getSize());

            /*$perfil_usado = array(count($data2));
            //inicializando quantidades de perfis
            for ($i = 0; $i < count($data2); $i++) {
                $perfil_usado[$i] = 0;
            }*/

            $usedProfiles = array_fill(0, count($this->profiles), 0);
            $profileSize = $this->profiles[$maxProfileSize]->getSize();

            //$perfil_usado[$tam_max_perfil] = floor($tam_linha / $data2[$tam_max_perfil]["tamanho"]);
            $usedProfiles[$maxProfileSize] = floor($lineSize / $profileSize);

            //var_dump($usedProfiles); die;

            //$resto_opc1 = (($tam_linha / $data2[$tam_max_perfil]["tamanho"]) - $perfil_usado[$tam_max_perfil]) * $data2[$tam_max_perfil]["tamanho"];
            $remaining = (($lineSize / $profileSize) - $usedProfiles[$maxProfileSize]) * $profileSize;

            //var_dump($remaining); die;
            // definição do primeiro maior perfil em relação à resto_opc1
            /*$primeiro_maior_opc1 = 0;
            for ($i = count($data2) - 1; $i >= $tam_max_perfil; $i--) {
                $primeiro_maior_opc1 = $i;
                if (($resto_opc1 - $data2[$i]["tamanho"]) < 0) {
                    break;
                }
            }*/

            $firstOptionSize = 0;
            for ($j = count($this->profiles) - 1; $j >= $maxProfileSize; $j--) {
                $firstOptionSize = $j;
                if (($remaining - $this->profiles[$j]->getSize()) < 0) {
                    break;
                }
            }

            //var_dump($firstOptionSize); die;

            /*
            if (($resto_opc1 * 2) > $data2[$primeiro_maior_opc1]["tamanho"]) {
                if ($tam_max_perfil == $primeiro_maior_opc1) {
                    $perfil_usado[$tam_max_perfil] = ($perfil_usado[$tam_max_perfil] * 2) + 2;
                } else {
                    $perfil_usado[$tam_max_perfil] = ($perfil_usado[$tam_max_perfil] * 2);
                    $perfil_usado[$primeiro_maior_opc1] += 2;
                }
            } else {
                $perfil_usado[$tam_max_perfil] *= 2;
                if ($tam_max_perfil == $primeiro_maior_opc1) {
                    $perfil_usado[$tam_max_perfil] += 1;
                } else {
                    $perfil_usado[$primeiro_maior_opc1] += 1;
                }
            }*/

            if (($remaining * 2) > $this->profiles[$firstOptionSize]->getSize()) {
                if ($maxProfileSize == $firstOptionSize) {
                    $usedProfiles[$maxProfileSize] = ($usedProfiles[$maxProfileSize] * 2) + 2;
                } else {
                    $usedProfiles[$maxProfileSize] = ($usedProfiles[$maxProfileSize] * 2);
                    $usedProfiles[$firstOptionSize] += 2;
                }
            } else {
                $usedProfiles[$maxProfileSize] *= 2;
                if ($maxProfileSize == $firstOptionSize) {
                    $usedProfiles[$maxProfileSize] += 1;
                } else {
                    $usedProfiles[$firstOptionSize] += 1;
                }
            }

            /*$tamanho_usado = 0;
            for ($i = 0; $i < count($data2); $i++) {
                $tamanho_usado += $perfil_usado[$i] * $data2[$i]["tamanho"];
            }*/

            $usedSize = 0;
            for ($k = 0; $k < $countProfiles; $k++) {
                $usedSize += $usedProfiles[$k] * $this->profiles[$k]->getSize();
            }

            //$sobra = $tamanho_usado - ($tam_linha * 2);
            $leftover = $usedSize - ($lineSize * 2);

            //var_dump($usedSize); die;

            /*if ($sobra > 2) {
                $perfil_usado[$tam_max_perfil] -= 1;

                //recalculando sobra
                $tamanho_usado = 0;
                for ($i = 0; $i < count($data2); $i++) {
                    $tamanho_usado += $perfil_usado[$i] * $data2[$i]["tamanho"];
                }
                $sobra = abs($tamanho_usado - ($tam_linha * 2));

                $a = 0;
                for ($i = count($data2) - 1; $i >= $tam_max_perfil; $i--) {
                    $a = $i;
                    if (($sobra - $data2[$i]["tamanho"]) < 0) {
                        break;
                    }
                }

                $valor = $data2[$a]["tamanho"];
                $valor_dividido = $valor / 2;
                $key = false;
                for ($i = 0; $i < count($data2); $i++) {
                    if ($data2[$i]["tamanho"] == $valor_dividido) {
                        $key = $i;
                    }
                }
                if ($key == false) {
                    $perfil_usado[$a] += 1;
                } else {
                    $perfil_usado[$key] += 2;
                }
            }*/

            if ($leftover > 2) {

                $usedProfiles[$maxProfileSize] -= 1;

                $usedSize = 0;
                for ($k2 = 0; $k2 < $countProfiles; $k2++) {
                    $usedSize += $usedProfiles[$k2] * $this->getProfile($k2)->getSize();
                }

                $leftover = abs($usedSize - ($lineSize * 2));

                $a = 0;
                for ($k3 = $countProfiles - 1; $k3 >= $maxProfileSize; $k3--) {
                    $a = $k3;
                    if (($leftover - $this->getProfile($k3)->getSize()) < 0) {
                        break;
                    }
                }

                $baseSize = $this->getProfile($a)->getSize();
                $baseSizeSplit = $baseSize / 2;
                $key = false;
                for ($k4 = 0; $k4 < $countProfiles; $k4++) {
                    if ($this->getProfile($k4)->getSize() == $baseSizeSplit) {
                        $key = $k4;
                    }
                }

                if (!$key) {
                    //$this->profiles[$a] += 1;
                    $this->getProfile($a)->setSize(
                        $this->getProfile($a)->getSize() + 1
                    );
                } else {
                    //$perfil_usado[$key] += 2;
                    $this->getProfile($key)->setSize(
                        $this->getProfile($key)->getSize() + 2
                    );
                }
            }

            $junction = array_sum($usedProfiles);

            if (($junction % 2) != 0) {
                $junction += 1;
            }
            $junction -= 2;

            for ($x1 = 0; $x1 < $countProfiles; $x1++) {
                $usedProfiles[$x1] *= $linesOfModules;
            }

            //$juncao *= $qte_linhas_mod;
            $junction *= $linesOfModules;
            //$term_final = 4 * $qte_linhas_mod;
            $endTerminal = 4 * $linesOfModules;
            //$term_inter = ($qte_mod - 1) * 2 * $qte_linhas_mod;
            $inTerminal = ($quantityModules - 1) * 2 * $linesOfModules;
            //$perfil_chapa_meio = ($term_final + $term_inter);
            $profileMiddlePlate = ($endTerminal + $inTerminal);
            $base = 2 * (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;

            //var_dump($base); die;

            if ($quantityModules == 1) {
                $base = 4 * $linesOfModules;
            }

            //$parafuso_martelo = $base;
            $screwHammer = $base;
            //$porca_m10 = $base;
            $nutM10 = $base;
            //$parafuso_est = $base;
            $screwStr = $base;

            //$parafuso_auto = ceil(4 * (($tam_linha / 0.4) + 1)) * $qte_linhas_mod;
            //$parafuso_auto = 4 * (ceil(($tam_linha) / 0.4) + 1) * $qte_linhas_mod;
            $screwAuto = 4 * (ceil(($lineSize) / 0.4) + 1) * $linesOfModules;
            //var_dump($screwAuto); die;
            //if ($tipo_telhado == 4) {

            if (self::ROOF_SHEET_METAL_PFM == $this->roofType) {
                //$parafuso_auto = $perfil_chapa_meio * 4;
                $screwAuto = $profileMiddlePlate * 4;
            }

            //var_dump($screwAuto); die;

            $speedClip = $screwAuto / 2;
            //$parafuso_auto_meio = $perfil_chapa_meio * 4;
            $triangle = (ceil(($lineSize - (2 * 0.35)) / 1.65) + 1) * $linesOfModules;
            //$fita = 2 * (ceil(($tam_linha) / 0.4) + 1) * 0.065 * $qte_linhas_mod;
            $plate = ($screwAuto / 2) * 0.1;

            $totalJunction += $junction;
            for ($z = 0; $z < $countProfiles; $z++) {
                $totalUsedProfiles[$z] += $usedProfiles[$z];
            }

            $totalEndTerminal += $endTerminal;                  // OK
            $totalInTerminal += $inTerminal;                    // OK
            $totalProfileMiddlePlate += $profileMiddlePlate;    //
            $totalBase += $base;                                //
            $totalScrewHammer += $screwHammer;                  //
            $totalNutM10 += $nutM10;                            //
            $totalScrewStr += $screwStr;                        //
            $totalScrewAuto += $screwAuto;                      //
            $totalSpeedClip += $speedClip;                      //
            $totalTriangle += $triangle;                        //
            $totalPlate += $plate;                              //

            /*var_dump($totalEndTerminal);
            var_dump($totalInTerminal);
            var_dump($totalProfileMiddlePlate);
            var_dump($totalBase);
            var_dump($totalScrewHammer);
            var_dump($totalNutM10);
            var_dump($totalScrewStr);
            var_dump($totalScrewAuto);
            var_dump($totalSpeedClip);
            var_dump($totalTriangle);
            var_dump($totalBase);
            var_dump($totalPlate);
            die;*/
        }

        //var_dump($this->getItemByType(Item::TYPE_JUNCTION));

        // Junction
        if(null != $junction = $this->getItemByType(Item::TYPE_JUNCTION))
            $junction->setQuantity($totalJunction);

        // Terminals
        $terminalFinal->setQuantity($totalEndTerminal);
        $terminalMiddle->setQuantity($totalInTerminal);

        //var_dump($this->profiles); die;



        //var_dump($this->getItemByType(Item::TYPE_JUNCTION));
        //var_dump($totalJunction); die;

        // TODO : Continue components here!
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {

    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function isVertical()
    {
        return self::POSITION_VERTICAL == $this->position;
    }

    /**
     * @inheritDoc
     */
    public function isHorizontal()
    {
        return self::POSITION_HORIZONTAL == $this->position;
    }

    /**
     * @param int $position
     * @return Structure
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getModulesPerLine(ItemInterface $item)
    {
        $modulesPerLine = self::MODULES_PER_LINE;

        if ($item->getQuantity() > 52) {
            $modulesPerLine = 18;
        }

        if ($this->isHorizontal()) {

            $modulesPerLine = 6;

            if (60 == $item->getCellNumber()) {
                $modulesPerLine = 7;

                if ($item->getQuantity() > 52) {
                    $modulesPerLine = 11;
                }
            }
        }

        return $modulesPerLine;
    }

    /**
     * @param int $modulesPerLine
     * @return Structure
     */
    public function setModulesPerLine($modulesPerLine)
    {
        $this->modulesPerLine = $modulesPerLine;
        return $this;
    }

    /**
     * @inheritDoc
     */
    /*public function getDimension()
    {
        return self::POSITION_VERTICAL == $this->position ? $this->module->getWidth() : $this->module->getLength() ;
    }*/

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     * @return Structure
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @param array $components
     * @return Structure
     */
    public function setComponents($components)
    {
        $this->components = $components;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addItem(ItemInterface $item)
    {
        if(is_null($this->roofType))
            $this->exception('To add items, the roof type must be set');

        $this->validateItem($item);

        //if(!$item->is(Item::TYPE_PROFILE)) {
        foreach ($this->items as $currentItem) {
            if (($item->getType() == $currentItem->getType()) && ($item->getSubtype() == $currentItem->getSubtype())) {
                throw new \InvalidArgumentException(sprintf('A component with type [%s] already exists', $item->getType()));
            }
        }
        //}

        $this->items[] = $item;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeItem(ItemInterface $item)
    {
        // TODO: Implement removeComponent() method.
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Create groups distribution
     */
    /*public function handleGroups(ItemInterface $item)
    {
        $this->groups = [];
        $totalModules = $item->getQuantity();
        $modulesPerLine = $this->getModulesPerLine($item);
        $remaining = $totalModules;

        for($i = 0; $i < ceil($totalModules / $modulesPerLine); $i++){
            $this->groups[$i] = $remaining;
            if($remaining > $modulesPerLine){
                $this->groups[$i] = $modulesPerLine;
                $remaining -= $modulesPerLine;
            }
        }
    }*/

    /**
     * Validate profile instances
     *
     * @param array $profiles
     */
    private function validateProfiles(array &$profiles)
    {
        foreach ($this->profiles as $profile) {
            if (!$profile instanceof ProfileInterface) {
                $this->exception('Profile instance is not valid');
            }
        }

        uasort($profiles, function (ProfileInterface $a, ProfileInterface $b) {
            return $b->getSize() < $a->getSize();
        });
    }

    /**
     * @param $type
     * @return ItemInterface
     */
    private function getItemByType($type, $checkSubtype = false)
    {
        $items = array_filter($this->items, function (ItemInterface $item) use ($type) {
            return $item->is($type);
        });

        return current($items);
    }

    /**
     * @param $key
     * @return ProfileInterface
     */
    private function getProfile($key)
    {
        return $this->profiles[$key];
    }

    /**
     * Validate data structure after calculation process
     */
    private function validate()
    {
        if (!$this->module)
            $this->exception('Module is not defined');

        if (is_null($this->roofType))
            $this->exception('Roof Type is not defined');

        if(!$this->getItemByType(Item::TYPE_BASE))
            $this->exception('Base is not defined');

        if(!$this->getItemByType(Item::TERMINAL_FINAL))
            $this->exception('Terminal Middle is not defined');

        if(!$this->getItemByType(Item::TERMINAL_FINAL))
            $this->exception('Terminal Final is not defined');

        if(!$this->getItemByType(Item::TYPE_JUNCTION)
            && (in_array($this->roofType, [self::ROOF_ROMAN_AMERICAN, self::ROOF_CEMENT, self::ROOF_SHEET_METAL])))
            $this->exception('Junction is not defined');
    }

    /**
     * @param ItemInterface $item
     */
    private function validateItem(ItemInterface $item)
    {
        if (!$item->isValid()) {
            $this->exception('This item is not valid');
        }

        if($item->is(Item::TYPE_BASE)){

            $subtypes = [
                self::ROOF_ROMAN_AMERICAN   => [Item::BASE_HOOK],
                self::ROOF_CEMENT           => [Item::BASE_SCREW_STRUCTURAL],
                self::ROOF_FLAT_SLAB        => [Item::BASE_TRIANGLE_VERTICAL, Item::BASE_TRIANGLE_HORIZONTAL],
                self::ROOF_SHEET_METAL      => [Item::BASE_SCREW_DRILLING],
                self::ROOF_SHEET_METAL_PFM  => [Item::BASE_SCREW_DRILLING]
            ];

            if(!in_array($item->getSubtype(), $subtypes[$this->roofType])){
                $this->exception('Unsupported base type');
            }
        }

        if($item->is(Item::TYPE_TERMINAL)){
            if(is_null($item->getSize())){
                $this->exception('Terminal size is not defined');
            }
        }
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
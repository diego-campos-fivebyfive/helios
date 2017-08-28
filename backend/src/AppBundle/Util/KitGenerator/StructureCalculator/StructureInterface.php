<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 23/06/17
 * Time: 15:14
 */

namespace AppBundle\Util\KitGenerator\StructureCalculator;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollectionInterface;

/**
 * Interface StructureInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface StructureInterface
{
    const MAKER_SICES_SOLAR     = 1;    // Fabricante Sices Solar
    const MAKER_K2_SYSTEM       = 2;    // Fabricante K2 System

    const ROOF_ROMAN_AMERICAN   = 0;    // Telhas Romanas e Americanas
    const ROOF_FIBERGLASS       = 1;    // Telhas de Fibrocimento
    const ROOF_FLAT_SLAB        = 2;    // Laje Plana
    const ROOF_SHEET_METAL      = 3;    // Chapa Metalica
    const ROOF_SHEET_METAL_PFM  = 4;    // Chapa Metalica (Perfil de 0,5m)

    /**
     * Positions
     */
    const POSITION_VERTICAL     = 0;
    const POSITION_HORIZONTAL   = 1;

    /**
     * Default modules per line
     */
    const MODULES_PER_LINE      = 12;

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $maker
     * @return StructureInterface
     */
    public function setMaker($maker);

    /**
     * @return int
     */
    public function getMaker();

    /**
     * @param ModuleInterface $module
     * @return StructureInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * @param array $profiles
     * @return StructureInterface
     */
    public function setProfiles(array $profiles);

    /**
     * @return array
     */
    public function getProfiles();

    /**
     * @param $roofType
     * @return mixed
     */
    public function setRoofType($roofType);


    /**
     * @return mixed
     */
    public function getRoofType();

    /**
     * @return bool
     */
    public function isVertical();

    /**
     * @return bool
     */
    public function isHorizontal();

    /**
     * @param ItemInterface $item
     * @return StructureInterface
     */
    public function addItem(ItemInterface $item);

    /**
     * @param ItemInterface $item
     * @return StructureInterface
     */
    public function removeItem(ItemInterface $item);

    /**
     * @return array
     */
    public function getItems();

    /**
     * @param $type
     * @return ItemInterface|null
     */
    public function getItem($type);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function calculate();
}
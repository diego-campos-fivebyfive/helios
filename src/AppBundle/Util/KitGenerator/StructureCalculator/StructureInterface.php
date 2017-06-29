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
    const ROOF_ROMAN_AMERICAN   = 0;    // Telhas Romanas e Americanas
    const ROOF_CEMENT           = 1;    // Telhas de Fibrocimento
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
     * @param $position
     * @return mixed
     */
    public function setPosition($position);

    /**
     * @return mixed
     */
    public function getPosition();

    /**
     * @return bool
     */
    public function isVertical();

    /**
     * @return bool
     */
    public function isHorizontal();

    /**
     * @param $modulesPerLine
     * @return mixed
     */
    public function setModulesPerLine($modulesPerLine);

    /**
     * @return mixed
     */
    public function getModulesPerLine(ItemInterface $item);

    /**
     * @param $totalModules
     * @return StructureInterface
     */
    public function setTotalModules($totalModules);

    /**
     * @return int
     */
    public function getTotalModules();

    /**
     * @return int|float
     */
    //public function getMaxProfileSize(ItemInterface $item);

    /**
     * @return float
     */
    //public function getDimension();

    /**
     * @param $group
     * @return mixed
     */
    public function setGroups($group);

    /**
     * @return mixed
     */
    public function getGroups();

    /**
     * @param CombinedCollectionInterface $combinedCollection
     * @return StructureInterface
     */
    public function setCombinedCollection(CombinedCollectionInterface $combinedCollection);

    /**
     * @return CombinedCollectionInterface
     */
    public function getCombinedCollection();

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
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function calculate();
}
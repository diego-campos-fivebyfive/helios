<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 23/06/17
 * Time: 15:14
 */

namespace AppBundle\Util\KitGenerator\Structure;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollectionInterface;

/**
 * Interface StructureInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface StructureInterface
{
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
     * @param $modulesPerLine
     * @return mixed
     */
    public function setModulesPerLine($modulesPerLine);

    /**
     * @return mixed
     */
    public function getModulesPerLine();

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


}
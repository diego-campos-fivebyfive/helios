<?php


namespace AppBundle\Util\KitGenerator\StructureCalculator;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollectionInterface;
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
     * @var array
     */
    private $groups;

    /**
     * @var array
     */
    private $components;


    private $combinedCollection;

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
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
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
    public function getModulesPerLine()
    {
        return $this->modulesPerLine;
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
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
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


}
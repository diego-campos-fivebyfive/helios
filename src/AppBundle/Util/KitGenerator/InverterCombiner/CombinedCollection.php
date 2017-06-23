<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CombinedCollection
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class CombinedCollection extends ArrayCollection implements CombinedCollectionInterface
{

    private $module;


    /**
     * @inheritDoc
     */
    public function __construct(array $elements = array())
    {
        $this->checkElements($elements);

        parent::__construct($elements);
    }

    /**
     * @inheritDoc
     */
    public function add($element)
    {
        return $this->addCombined($element);
    }

    /**
     * @inheritDoc
     */
    public function addCombined(CombinedInterface $combined)
    {
        if (!$this->contains($combined)) {
            parent::add($combined);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeCombined(CombinedInterface $combined)
    {
        if ($this->contains($combined)) {
            parent::removeElement($combined);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCombinations()
    {
        return $this->toArray();
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

    /**
     * @param array $elements
     */
    private function checkElements(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!$element instanceof CombinedInterface) {
                throw new \InvalidArgumentException(sprintf('This collection accept only instance of %s', CombinedInterface::class));
            }
        }
    }
}
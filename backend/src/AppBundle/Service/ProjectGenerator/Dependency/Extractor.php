<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\ProjectElementInterface;
use AppBundle\Entity\Component\ProjectInterface;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class Extractor
 * This class extracts the dependency collection from a baseline project,
 * indexing them by type keys
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Extractor
{
    /**
     * @var array
     */
    private $dependencies = [];

    /**
     * @var array
     */
    private $sources = ['inverter', 'module', 'string_box', 'structure'];

    /**
     * @param ProjectInterface $project
     * @return array
     */
    public function fromProject(ProjectInterface $project)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->sources as $source){

            $path = Inflector::pluralize(Inflector::pluralize('project' . $source));
            $projectComponents = $accessor->getValue($project, $path);

            if(!empty($projectComponents)) {
                /** @var ProjectElementInterface $projectComponent */
                foreach ($projectComponents as $projectComponent) {
                    $this->fromComponent($accessor->getValue($projectComponent, $source), $projectComponent->getQuantity());
                }
            }
        }

        return $this->dependencies;
    }

    /**
     * @param ComponentInterface $component
     * @param $quantity
     */
    private function fromComponent(ComponentInterface $component, $quantity)
    {
        $this->merge($component->getDependencies(), $quantity);
    }

    /**
     * @param array $dependencies
     * @param $quantity
     */
    private function merge(array $dependencies, $quantity)
    {
        foreach ($dependencies as $dependency){

            $id = $dependency['id'];
            $type = $dependency['type'];

            if(!array_key_exists($type, $this->dependencies))
                $this->dependencies[$type] = [];

            if(!array_key_exists($id, $this->dependencies[$type]))
                $this->dependencies[$type][$id] = 0;

            $this->dependencies[$type][$id] += ($quantity * $dependency['ratio']);
        }
    }

    /**
     * @return Extractor
     */
    public static function create()
    {
        return new self();
    }
}

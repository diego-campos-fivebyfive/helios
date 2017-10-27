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

use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\ProjectInterface;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class Resolver
 * This class resolve dependencies from a project,
 * - Check promotional
 * - Resolve accumulated relationships
 * - Creates new dependency associations
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Resolver
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var bool
     */
    private $strictPromotional = true;

    /**
     * Resolver constructor.
     * @param Loader $loader
     */
    function __construct(ContainerInterface $container)
    {
        $this->loader = Loader::create($container);
    }

    /**
     * @param ProjectInterface $project
     */
    public function resolve(ProjectInterface $project)
    {
        $dependencies = Extractor::create()->fromProject($project);
        $collection = $this->loader->load(self::normalize($dependencies));
        $checkPromotional = $project->isPromotional() && $this->strictPromotional;

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($collection as $type => $components){

            /** @var ComponentInterface $component */
            foreach ($components as $component) {

                if($checkPromotional && !$component->isPromotional()) continue;

                $quantity = $dependencies[$type][$component->getId()];

                $id = $component->getId();
                $related = sprintf('project' . ucfirst($type));
                $getter = Inflector::pluralize($related);

                $projectComponent = $accessor->getValue($project, $getter)->filter(function($related) use($id, $type, $accessor){
                    return $id === $accessor->getValue($related, $type)->getId();
                })->first();

                if($projectComponent){
                    $quantity = $accessor->getValue($projectComponent, 'quantity') + $quantity;
                }else {

                    $projectComponent = new ProjectVariety();
                    $accessor->setValue($projectComponent, 'project', $project);
                    $accessor->setValue($projectComponent, $type, $component);
                }

                $accessor->setValue($projectComponent, 'quantity', $quantity);
            }
        }
    }

    /**
     * @param array $extracted
     * @return array
     */
    private static function normalize(array $extracted = [])
    {
        $normalized = [];
        foreach ($extracted as $type => $dependencies) {
            $normalized[$type] = array_keys($dependencies);
        }

        return $normalized;
    }

    /**
     * @return Resolver
     */
    public static function create(ContainerInterface $container)
    {
        return new self($container);
    }
}

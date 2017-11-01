<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Parameter;
use AppBundle\Service\Filter\EntityFilter;
use Doctrine\Common\Inflector\Inflector;

/**
 * This class provide a dynamic mechanism for defaults generator parameters
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class DefaultsResolver
{
    // Throws an exception if default is not resolved
    const STRATEGY_EXCEPTION = 0;

    // Set null if default is not resolved
    const STRATEGY_NULLABLE  = 1;

    // Use original default if default is not resolved
    const STRATEGY_ABSTAIN = 2;

    /**
     * @var EntityFilter
     */
    private $filter;

    /**
     * @var array
     */
    private $defaults = [];

    /**
     * @var int
     */
    private $strategy = self::STRATEGY_ABSTAIN;

    /**
     * @param EntityFilter $filter
     */
    function __construct(EntityFilter $filter)
    {
        $this->filter = $filter;
        $this->defaults = self::defaults();
    }

    /**
     * Resolve dynamic generator defaults
     * @return array
     */
    public function resolve()
    {
        $this->module();
        $this->promoEndAt();
        $this->shippingIncluded();
        $this->maker('inverter');
        $this->maker('structure');
        $this->maker('string_box');

        return $this->defaults;
    }

    /**
     * Revolve default module id
     */
    public function module()
    {
        $id = null;
        $module = $this->resolveArgs(Module::class, ['id' => $this->defaults['module']]);

        if($module instanceof Module){
            $id = $module->getId();
        }

        $this->resolveDefault('module', $id);
    }

    /**
     * Revolve default promoEndAt
     */
    public function promoEndAt()
    {
        $parameter = $this->resolveParameters(Parameter::class, ['id' => 'platform_settings']);
        if($parameter instanceof Parameter && $parameter->get('enable_promo') ) {
            $this->resolveDefault('promo_end_at', (new \DateTime($parameter->get('promo_end_at')['date']))->format('d/m/Y'));
        }
    }

    /**
     * Resolve default shippingIncluded
     */
    public function shippingIncluded()
    {
        $parameter = $this->resolveParameters(Parameter::class, ['id' => 'platform_settings']);
        if($parameter instanceof Parameter && $parameter->get('shipping_included'))
            $this->resolveDefault('shipping_included', $parameter->get('shipping_included'));
    }

    /**
     * @param $type
     */
    private function maker($type)
    {
        $tag = sprintf('%s_maker', $type);
        $class = str_replace('Module', Inflector::classify($type), Module::class);

        $id = null;
        $entity = $this->resolveArgs($class, ['maker' => $this->defaults[$tag]]);

        if($entity){
            $id =  $entity->getMaker()->getId();
        }

        $this->resolveDefault($tag, $id);
    }

    /**
     * @param $class
     * @param array $arguments
     * @return null|object Entity instance
     */
    private function resolveArgs($class, array $arguments, $forceStrictArgs = true)
    {
        $strictArgs = ['available' => true, 'status' => true];

        $parameters = array_merge($arguments, $strictArgs);

        $entity = $this->resolveParameters($class, $parameters);

        if(!$entity && $forceStrictArgs){
            $entity = $this->resolveArgs($class, [], false);
        }

        return $entity;
    }

    /**
     * @param $class
     * @param array $parameters
     * @return mixed
     */
    private function resolveParameters($class, array $parameters)
    {
        $this->filter->fromClass($class);

        foreach ($parameters as $parameter => $value){
            $this->filter->equals($parameter, $value);
        }

        return $this->filter->getOne();
    }

    /**
     * @param array $defaults
     * @return DefaultsResolver
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param int $strategy
     * @return DefaultsResolver
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return int
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Resolve default value and apply strategy
     * @param $default
     * @param $value
     */
    private function resolveDefault($default, $value)
    {
        if(is_null($value)){
            switch ($this->strategy){
                case self::STRATEGY_EXCEPTION:
                    throw new \InvalidArgumentException(sprintf('The default parameter [%s] could not be resolved', $default));
                    break;

                case self::STRATEGY_ABSTAIN:
                    $value = self::defaults()[$default];
                    break;
            }
        }

        $this->defaults[$default] = $value;
    }

    /**
     * @return array
     */
    private static function defaults()
    {
        return [
            'address' => null,
            'latitude' => null,
            'longitude' => null,
            'customer' => null,
            'stage' => null,
            'roof_type' => 'ROOF_ROMAN_AMERICAN',
            'source' => 'consumption',
            'power' => 0,
            'consumption' => 0,
            'use_transformer' => true,
            'power_transformer' => 0,
            'grid_voltage' => '127/220',
            'grid_phase_number' => 'Biphasic',
            'module' => 32433,
            'inverter_maker' => 60627,
            'structure_maker' => 61211,
            'string_box_maker' => 61209,
            'is_promotional' => false,
            'shipping_included' => false,
            'promo_end_at' => null,
            'errors' => []
        ];
    }
}

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
use AppBundle\Entity\Component\Project;
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
        $this->shippingIncluded();
        $this->maker('inverter');
        $this->maker('structure');
        $this->maker('string_box');
        $this->fdi('min');
        $this->fdi('max');
        $this->promoNotice();
        $this->promoBackground();

        return $this->defaults;
    }

    /**
     * Revolve default module id
     */
    public function module()
    {
        if($this->hasDefault('module')) return;

        $qb = $this->filter->fromClass(Module::class)->qb();

        $qb
            ->orderBy('m.position', 'asc')
            ->andWhere(
                $qb->expr()->like(
                    'm.generatorLevels',
                    $qb->expr()->literal('%"'.$this->defaults['level'].'"%')
            )
        )->setMaxResults(1);

        $module = $qb->getQuery()->getOneOrNullResult();

        $id = $module->getId();

        $this->resolveDefault('module', $id);
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
     * Resolve default promoNotice
     */
    public function promoNotice()
    {
        $parameter = $this->resolveParameters(Parameter::class, ['id' => 'platform_settings']);
        if($parameter instanceof Parameter && $parameter->get('promo_notice'))
            $this->resolveDefault('promo_notice', $parameter->get('promo_notice'));
    }

    /**
     * Resolve default promoBackground
     */
    public function promoBackground()
    {
        $parameter = $this->resolveParameters(Parameter::class, ['id' => 'platform_settings']);
        if($parameter instanceof Parameter && $parameter->get('promo_background'))
            $this->resolveDefault('promo_background', $parameter->get('promo_background'));
    }

    /**
     * @param $type
     */
    private function fdi($type)
    {
        $tag = sprintf('fdi_%s', $type);
        $value = 'min' == $type ? 0.75 : 1.2 ;

        $parameter = $this->resolveParameters(Parameter::class, ['id' => 'platform_settings']);

        if($parameter instanceof Parameter && $parameter->get($tag))
            $value = (float)$parameter->get($tag);

        $this->resolveDefault($tag, $value);
    }

    /**
     * @param $type
     */
    private function maker($type)
    {
        $tag = sprintf('%s_maker', $type);

        if($this->hasDefault($tag)) return;

        $class = str_replace('Module', Inflector::classify($type), Module::class);

        $qb = $this->filter->fromClass($class)->qb();

        $qb
            ->select('c')
            ->from($class, 'c')
            ->orderBy('c.position', 'asc')
            ->andWhere(
                $qb->expr()->like(
                    'c.generatorLevels',
                    $qb->expr()->literal('%"'.$this->defaults['level'].'"%')
                )
            )->setMaxResults(1);

        $component = $qb->getQuery()->getOneOrNullResult();

        $id = $component->getMaker()->getId();

        $this->resolveDefault($tag, $id);
    }

    /**
     * @param $class
     * @param array $parameters
     * @return mixed
     */
    private function resolveParameters($class, array $parameters, array $orderBy = [])
    {
        $this->filter->fromClass($class);

        foreach ($parameters as $parameter => $value){
            $this->filter->equals($parameter, $value);
        }

        foreach ($orderBy as $field => $direction){
            $this->filter->order($field, $direction);
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
     * @param $default
     * @return bool
     */
    private function hasDefault($default)
    {
        return array_key_exists($default, $this->defaults) && !is_null($this->defaults[$default]);
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
            'roof_type' => 'ROOF_CEMENT',
            'source' => 'consumption',
            'power' => 0,
            'consumption' => 0,
            'use_transformer' => true,
            'power_transformer' => 0,
            'grid_voltage' => '127/220',
            'grid_phase_number' => 'Biphasic',
            'module' => null,
            'inverter_maker' => null,
            'structure_maker' => null,
            'string_box_maker' => null,
            'is_promotional' => false,
            'shipping_included' => false,
            'promo_notice' => null,
            'promo_background' => null,
            'fdi_min' => null,
            'fdi_max' => null,
            'level' => null,
            'errors' => []
        ];
    }
}

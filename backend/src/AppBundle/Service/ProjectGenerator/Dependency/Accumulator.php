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

use AppBundle\Entity\Component\Variety;
use AppBundle\Entity\Component\ComponentInterface;

/**
 * Class Accumulator
 * This class coordinates the dependencies accumulated in a generation cycle,
 * preventing redundant inserts, depending on the dependency settings for project components
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Accumulator
{
    const VARIETY = 'variety';

    private $components = [
        self::VARIETY => [
            'class' => Variety::class,
            'items' => []
        ]
    ];

    /**
     * @param ComponentInterface $component
     * @param $quantity
     * @return $this
     */
    public function add(ComponentInterface $component, $quantity)
    {
        $type = $this->type($component);
        $id = $component->getId();

        if (!array_key_exists($id, $this->components[$type]['items'])){

            $this->components[$type]['items'][$id] = [
                'component' => $component,
                'quantity' => 0
            ];
        }

        $this->components[$type]['items'][$id]['quantity'] += $quantity;

        return $this;
    }

    /**
     * @param $type
     * @return array
     */
    public function get($type)
    {
        return $this->components[$type]['items'];
    }

    /**
     * @param ComponentInterface $component
     * @return int|null|string
     */
    public function type(ComponentInterface $component)
    {
        $class = get_class($component);
        foreach ($this->components as $type => $config)
            if($class == $config['class']) return $type;

        return null;
    }

    /**
     * @return Accumulator
     */
    public static function create()
    {
        return new self();
    }
}

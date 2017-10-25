<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Util;

use AppBundle\Manager\ParameterManager;

/**
 * Class PlatformCounter
 * This class generates sequential counting according to the registry key
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class PlatformCounter
{
    private $id = 'platform_counter';

    /**
     * @var ParameterManager
     */
    private $manager;

    /**
     * @var \AppBundle\Entity\Parameter
     */
    private $parameter;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var array
     */
    private $counters = [
        'orders' => [
            'strategy' => 'date'
        ],
        'projects' => [
            'strategy' => 'date'
         ],
    ];

    /**
     * PlatformCounter constructor.
     * @param ParameterManager $manager
     */
    public function __construct(ParameterManager $manager)
    {
        $this->manager = $manager;

        $this->initialize();
    }

    /**
     * @param \DateTime|null $date
     * @return \DateTime
     */
    public function date(\DateTime $date = null)
    {
        if($date) $this->date = $date;

        $this->refresh();

        return $this->date;
    }

    /**
     * @param $key
     * @return int
     */
    public function current($key)
    {
        $this->check($key);

        $counter =  $this->parameter->get($key);

        return $counter['current'];
    }

    /**
     * @param $key
     * @return int
     */
    public function next($key)
    {
        $this->check($key);

        /** @var array $counter */
        $counter = $this->parameter->get($key);

        if(0 == $counter['current'] || ($this->date->format('Y-m-d') == $counter['date']->format('Y-m-d'))) {
            $this->define($key, $this->date, ($counter['current'] +1), true);
        }

        return $this->current($key);
    }

    /**
     * @param $key
     */
    private function check($key)
    {
        if(!array_key_exists($key, $this->counters)){
            throw new \InvalidArgumentException(sprintf('Invalid counter key [%s]', $key));
        }
    }

    /**
     * Initialize counters
     */
    private function initialize()
    {
        $this->parameter = $this->manager->findOrCreate($this->id);
        $this->date = new \DateTime();

        $this->refresh();
    }

    /**
     * Refresh counters
     */
    private function refresh()
    {
        foreach ($this->counters as $key => $config){

            /** @var array|null $counter */
            $counter = $this->parameter->get($key);

            if(!$counter || ($this->date->format('Y-m-d') != $counter['date']->format('Y-m-d'))){
                $this->define($key, $this->date, 0, false);
            }
        }

        $this->save();
    }

    /**
     * @param $key
     * @param \DateTime $date
     * @param $current
     */
    private function define($key, \DateTime $date, $current, $save = false){

        $this->parameter->set($key, [
            'date' => $date,
            'current' => $current
        ]);

        if($save) $this->save();
    }

    /**
     * Save counters
     */
    private function save()
    {
        $this->manager->save($this->parameter);
    }
}

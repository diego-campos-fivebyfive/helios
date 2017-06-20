<?php

namespace AppBundle\Service\Woopra;

use AppBundle\Service\SessionStorage;

class Manager
{
    private $id = 'woopra_events';

    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    function __construct(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @param $name
     * @param array $attributes
     * @return Event
     */
    public function createEvent($name, array $attributes)
    {
        $event = new Event($name, $attributes);

        $events = $this->sessionStorage->get($this->id, []);

        $events[$event->getId()] = $event;

        $this->sessionStorage->set($this->id, $events);

        return $event;
    }

    /**
     * @param $id
     * @return Event|null
     */
    public function getEvent($id)
    {
        $events = $this->getEvents();
        if(!empty($events)){
            foreach($events as $event){
                if($event instanceof Event && $event->getId() == $id){
                    return $event;
                }
            }
        }
        return null;
    }

    /**
     * @param Event $event
     */
    public function deleteEvent(Event $event)
    {
        $events = $this->getEvents();
        if(!empty($events) && array_key_exists($event->getId(), $events)) {
            unset($events[$event->getId()]);
            $this->sessionStorage->set($this->id, $events);
        }
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        $events = $this->sessionStorage->get($this->id);

        return $events;
    }
}
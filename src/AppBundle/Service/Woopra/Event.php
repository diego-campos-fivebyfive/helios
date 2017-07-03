<?php

namespace AppBundle\Service\Woopra;

class Event
{
    const STATUS_SENT = 'sent';
    const STATUS_OPEN = 'open';

    private $status;

    private $id;

    private $name;

    private $attributes;

    /**
     * Event constructor.
     * @param $name
     * @param array $attributes
     */
    function __construct($name, array $attributes = [])
    {
        $this->id = substr(md5(uniqid(time())), 0, 15);
        $this->name = $name;
        $this->status = self::STATUS_OPEN;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Event
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return Event
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return $this->status == self::STATUS_SENT;
    }
}
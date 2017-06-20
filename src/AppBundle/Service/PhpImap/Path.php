<?php

namespace AppBundle\Service\PhpImap;

class Path implements PathInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $port;

    /**
     * @var array
     */
    private $extras = [];

    /**
     * @inheritDoc
     */
    public function __construct($domain = null, $port = null, array $extras = [])
    {
        $this->domain = $domain;
        $this->port = $port;
        $this->extras = $extras;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->format();
    }
    

    /**
     * @inheritDoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExtra($extra)
    {
        if(!in_array($extra, $this->extras))
            $this->extras[] = $extra;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeExtra($extra)
    {
        $position = array_search($extra, $this->extras);

        if(is_integer($position)){
            unset($this->extras[$position]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @inheritDoc
     */
    public function format()
    {
        return sprintf(self::PATH, $this->domain, $this->port, !empty($this->extras) ? '/' . implode('/', $this->extras) : '');
    }
}
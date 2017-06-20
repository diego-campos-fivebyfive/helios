<?php

namespace AppBundle\Service\SolarEnergy\Nasa;

class SolarDeclination extends NasaYear implements SolarDeclinationInterface
{
    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        if(count($data))
            throw new \InvalidArgumentException('Solar declinations does not accept arguments');

        parent::__construct(self::getSolarDeclinations());
    }
}
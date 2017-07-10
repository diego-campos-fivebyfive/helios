<?php

namespace Tests\AppBundle\Util\ProjectPricing;

class TempMarkup
{
    private $initialPower;

    private $finalPower;

    private $markup;

    /**
     * @return mixed
     */
    public function getInitialPower()
    {
        return $this->initialPower;
    }

    /**
     * @param mixed $initialPower
     * @return TempMarkup
     */
    public function setInitialPower($initialPower)
    {
        $this->initialPower = $initialPower;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinalPower()
    {
        return $this->finalPower;
    }

    /**
     * @param mixed $finalPower
     * @return TempMarkup
     */
    public function setFinalPower($finalPower)
    {
        $this->finalPower = $finalPower;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @param mixed $markup
     * @return TempMarkup
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;
        return $this;
    }
}
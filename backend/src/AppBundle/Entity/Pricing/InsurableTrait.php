<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
trait InsurableTrait
{
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $insurance;

    /**
     * @inheritdoc
     */
    public function setInsurance($insurance)
    {
        $this->insurance = $insurance;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInsurance()
    {
        return $this->insurance;
    }
}
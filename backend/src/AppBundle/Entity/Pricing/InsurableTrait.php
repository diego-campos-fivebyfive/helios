<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Pricing;

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
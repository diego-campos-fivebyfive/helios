<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Pricing;

/**
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface InsurableInterface
{
    /**
     * @return float
     */
    public function getInsuranceQuota();

    /**
     * @param float $insurance
     * @return $this
     */
    public function setInsurance($insurance);

    /**
     * @return float
     */
    public function getInsurance();
}
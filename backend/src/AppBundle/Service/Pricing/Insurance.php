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
 * This class provide a default methods from insurable entities
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Insurance
{
    /**
     * Percent off base price
     */
    const PERCENT = 0.0065;

    /**
     * @param InsurableInterface $insurable
     */
    public static function insure(InsurableInterface $insurable)
    {
        $quota = $insurable->getInsuranceQuota();

        $insurable->setInsurance(self::calculate($quota));
    }

    /**
     * @param $quota
     * @param float $percent
     * @return float
     */
    public static function calculate($quota, $percent = self::PERCENT)
    {
        return ($quota * $percent);
    }
}
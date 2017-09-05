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
     * @param $isInsure
     */
    public static function apply(InsurableInterface $insurable, $isInsure)
    {
        if($isInsure){
            self::insure($insurable);
        }else{
            self::remove($insurable);
        }
    }

    /**
     * @param InsurableInterface $insurable
     */
    public static function insure(InsurableInterface $insurable)
    {
        $quota = $insurable->getInsuranceQuota();

        $insurable->setInsurance(self::calculate($quota));
    }

    /**
     * @param InsurableInterface $insurable
     */
    public static function remove(InsurableInterface $insurable)
    {
        $insurable->setInsurance(0);
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
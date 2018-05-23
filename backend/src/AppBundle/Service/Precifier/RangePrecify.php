<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Range;

/**
 * Class RangePrecify
 * @package AppBundle\Service\Precifier
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class RangePrecify
{
    /**
     * @param $metadata
     * @param $level
     * @param $costPrice
     * @param null $markup
     * @param null $powerRange
     * @return array
     */
    public static function calculate($metadata, $costPrice, $level = null, $markup = null, $powerRange = null)
    {
        $costPrice = $costPrice ? $costPrice : 0;

        $precifyAllLevels = ($markup === null || $powerRange === null) && !$level;

        if ($precifyAllLevels) {
            foreach ($metadata as $level => $levelRanges) {
                foreach ($levelRanges as $powerRange => $data) {
                    $markup = $data['markup'];

                    $levelRanges[$powerRange]['price'] = self::precify($costPrice, $markup);
                }
                $metadata[$level] = $levelRanges;
            }
        } else {
            $metadata[$level][$powerRange]['markup'] = $markup;

            $metadata[$level][$powerRange]['price'] = self::precify($costPrice, $markup);
        }

        return $metadata;
    }

    /**
     * @param $costPrice
     * @param $markup
     * @return float
     */
    private static function precify(float $costPrice, float $markup)
    {
        return round($costPrice * (1 + $markup) / (1 - Range::DEFAULT_TAX), 2);
    }
}

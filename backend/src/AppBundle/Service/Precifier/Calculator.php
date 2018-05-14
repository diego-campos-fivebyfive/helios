<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Range;

/**
 * Class Calculator
 * @package AppBundle\Service\Precifier
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Calculator
{
    /**
     * @param array $data
     * @param array $componentsRange
     * @return array
     */
    public static function precify(array $data, array $componentsRange)
    {
        $ranges = [];

        foreach ($componentsRange as $range) {
            $tag = self::generateAccessKey($range['family'], $range['component_id']);

            $ranges[$tag] = $range;
        }

        $level = $data['level'];
        $power = $data['power'];
        $groups = $data['groups'];

        $powerRange = self::identifyRange($power);

        $precifiedResults = [];

        foreach ($groups as $family => $components) {
            foreach ($components as $component_id) {
                $tag = self::generateAccessKey($family, $component_id);

                $price = $ranges[$tag]['metadata'][$level][$powerRange]['price'];

                $precifiedResults[$family][$component_id] = $price;
            }
        }

        return $precifiedResults;
    }

    /**
     * @param $power
     * @return mixed
     */
    public static function identifyRange($power)
    {
        foreach (Range::$powerRanges as $i => $basePower) {
            if ($power < $basePower) {
                return Range::$powerRanges[$i-1];
            }
        }

        return Range::$powerRanges[count(Range::$powerRanges)-1];
    }

    /**
     * @param $prefix
     * @param $suffix
     * @return string
     */
    private static function generateAccessKey($prefix, $suffix)
    {
        return $prefix . '_' . $suffix;
    }
}

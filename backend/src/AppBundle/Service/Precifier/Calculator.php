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
            $tag = self::generateAccessKey($range['family'], $range['componentId']);

            $ranges[$tag] = $range;
        }

        $level = $data['level'];
        $power = $data['power'];
        $groups = $data['groups'];

        $powerRange = self::identifyRange($power);

        $precifiedResults = [];

        foreach ($groups as $family => $components) {
            foreach ($components as $componentId => $projectElement) {
                $tag = self::generateAccessKey($family, $componentId);

                $price = $ranges[$tag]['metadata'][$level][$powerRange]['price'];

                if (is_array($projectElement)) {
                    foreach ($projectElement as $element) {
                        $precifiedResults[$family][$componentId][] = [
                            'price' => $price,
                            'projectElement' => $element
                        ];
                    }
                } else {
                    $precifiedResults[$family][$componentId]['price'] = $price;
                    $precifiedResults[$family][$componentId]['projectElement'] = $projectElement;
                }
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
        sort(Range::$powerRanges);

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

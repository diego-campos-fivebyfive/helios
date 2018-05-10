<?php

namespace AppBundle\Service\Precifier;

/**
 * Class Calculator
 * @package AppBundle\Service\Precifier
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Calculator
{
    /**
     * @var array
     */
    private static $powerRanges = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];
    // TODO: mover variável para entidade Range

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
    private static function identifyRange($power)
    {
        foreach (self::$powerRanges as $i => $basePower) {
            if ($power < $basePower) {
                return self::$powerRanges[$i-1];
            }
        }

        return self::$powerRanges[count(self::$powerRanges)-1];
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

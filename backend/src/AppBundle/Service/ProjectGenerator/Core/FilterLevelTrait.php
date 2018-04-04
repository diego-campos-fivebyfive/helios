<?php

namespace AppBundle\Service\ProjectGenerator\Core;

trait FilterLevelTrait
{

    /**
     * @param array $data
     * @param array $alternatives
     * @return array
     */
    public static function filterActives($level, array $data, array $alternatives = [])
    {

        foreach ($data as $key => $item) {
            if (null != $itemX = self::filterAlternativeItem($level, $item, $alternatives)) {
                $data[$key] = $itemX;
            } else {
                unset($data[$key]);
            }
        }

        uasort($data, function ($a, $b) {
            return $a["nominal_power"] > $b["nominal_power"];
        });

        return array_values($data);
    }

    /**
     * @param $item
     * @param array $alternatives
     * @return null
     */
    private static function filterAlternativeItem($level, $item, array &$alternatives = [])
    {

        if (!is_null($item)) {
            if (in_array($level, $item['levels'])) {
                return $item;
            } elseif(array_key_exists('alternative', $item)) {

                $key = array_search($item['alternative'], array_column($alternatives, 'id'));

                if(is_numeric($key)) {

                    $next = &$alternatives[$key];

                    if (!array_key_exists('visited', $next) || !$next['visited']) {
                        $next['visited'] = true;
                        return self::filterAlternativeItem($level, $next, $alternatives);
                    }
                }
            }
        }

        return null;
    }
}

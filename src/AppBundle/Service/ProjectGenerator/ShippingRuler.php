<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator;

/**
 * This class apply static shipping rules pricing
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
abstract class ShippingRuler
{
    const REGION_NORTH = 'north';
    const REGION_NORTHEAST = 'northeast';
    const REGION_SOUTH = 'south';
    const REGION_SOUTHEAST = 'southeast';
    const REGION_MIDWEST = 'midwest';

    /**
     * Apply shipping rules
     * @param $rule
     */
    public static function apply(&$rule)
    {
        if('self' == $rule['type']){
            self::defaults($rule);
        }else {
            self::definitions($rule);
            self::value($rule);
            self::markup($rule);
            self::shipping($rule);
        }
    }

    /**
     * Determine markup
     *
     * @param array $rule
     */
    private static function markup(array &$rule)
    {
        $markup = 30;
        if($rule['power'] > 10){
            $markup = $rule['power'] > 20 ? 10 : 20 ;
        }

        $rule['markup'] = $markup / 100;
    }

    /**
     * Calculate shipping by markup and value
     *
     * @param array $rule
     */
    private static function shipping(array &$rule)
    {
        $rule['shipping'] = $rule['value'] * (1 + $rule['markup']);
    }

    /**
     * Parse base shipping value
     *
     * @param array $rule
     */
    private static function value(array &$rule)
    {
        $value = $rule['price'] * $rule['percent'];
        $rule['value'] = $value < 400 ? 400 : $value;
    }

    /**
     * Initialize definitions
     *
     * @param array $rule
     */
    private static function definitions(array &$rule)
    {
        $rules = self::rules();
        $limit = null;
        $companies = [];
        foreach ($rules['limit'] as $key => $data){
            if(is_null($limit) || $rule['price'] > $limit){
                $limit = $key;
                $companies = $data;
            }else{
                break;
            }
        }

        self::company($companies, $rule);
    }

    /**
     * Filter company by region config
     *
     * @param array $companies
     * @param array $rule
     */
    private static function company(array $companies, array &$rule)
    {
        foreach($companies as $company => $config){

            if(array_key_exists($rule['region'], $config)){

                $percentConfig =  $config[$rule['region']];
                if(is_array($percentConfig)){
                    $percentConfig = $percentConfig[$rule['kind']];
                }
                $rule['percent'] = $percentConfig / 100;
                $rule['company'] = $company;
                break;
            }
        }
    }

    /**
     * Configured rules
     * @return array
     */
    private static function rules()
    {
        return [
            'limit' => [
                60000 => [
                    'mlt' => [
                        self::REGION_NORTH => 4.22,
                        self::REGION_NORTHEAST => 4.22,
                        self::REGION_MIDWEST => 3.1
                    ],
                    'ctp' => [
                        self::REGION_SOUTH => 3.6,
                        self::REGION_SOUTHEAST => 3.5
                    ]
                ],
                100000 => [
                    'ctp' => [
                        self::REGION_NORTH => 4.7,
                        self::REGION_NORTHEAST => 4.7,
                        self::REGION_MIDWEST => 4.3,
                        self::REGION_SOUTH => 3.6,
                        self::REGION_SOUTHEAST => [
                            'interior' => 3.5,
                            'sp-capital' => 1.9,
                            'rj-capital' => 2.1,
                            'mg-capital' => 2.3
                        ]
                    ]
                ],
                200000 => [
                    'ctp' => [
                        self::REGION_NORTH => 4.2,
                        self::REGION_NORTHEAST => 4.2,
                        self::REGION_MIDWEST => 4,
                        self::REGION_SOUTH => 3.2,
                        self::REGION_SOUTHEAST => [
                            'interior' => 3,
                            'sp-capital' => 1.55,
                            'rj-capital' => 1.6,
                            'mg-capital' => 2.1
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param array $rule
     */
    private static function defaults(array &$rule)
    {
        $rule['value'] = 0;
        $rule['markup'] = 0;
        $rule['company'] = null;
        $rule['state'] = null;
        $rule['kind'] = null;
        $rule['region'] = null;
        $rule['shipping'] = $rule['price'] * $rule['percent'];
    }
}
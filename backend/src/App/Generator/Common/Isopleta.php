<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Generator\Common;

/**
 * Class Isopleta
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Isopleta
{
    private static $data = [
        'XI' => [
            -70.13997,
            -71.521317,
            -50.634538,
            -50.634538,
            -60.306941,
            -46.719377,
            -58.404107,
            -57.614186,
            -57.86283,
            -56.482277,
            -56.482277,
            -47.312212
        ],
        'YI' => [
            0.549266,
            -4.662789,
            -4,
            -4,
            -15.610266,
            -15.350101,
            -17.074847,
            -18.491535,
            -21.013047,
            -31.059597,
            -31.059597,
            -22.50192625
        ],
        'XF' => [
            -57.560321,
            -55.214848,
            -49.072298,
            -38.246354,
            -46.719377,
            -42.609196,
            -44.646754,
            -47.977995,
            -52.108136,
            -52.108136,
            -49.333806,
            null
        ],
        'YF' => [
            6.156456,
            2.940973,
            0.30233,
            -2.852602,
            -15.350101,
            -19.508024,
            -23.152154,
            -25.189401,
            -24.396064,
            -24.396064,
            -28.978411,
            null
        ],
        'X5' => [
            null,
            null,
            null,
            -0.00044907064368,
            0.00007554121991,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        'X4' => [
            -0.00070015636619,
            null,
            null,
            -0.095001194335,
            0.019487413238,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        'X3' => [
            -0.17656367474,
            0.0042479796616,
            null,
            -7.9942453725,
            2.0021801002,
            null,
            0.0041396374736,
            0.0045803357581,
            null,
            null,
            -0.074598417305,
            null
        ],
        'X2' => [
            -16.578608149,
            0.88888169387,
            null,
            -334.43773009,
            102.35654807,
            0.29151294456,
            0.6118549898,
            0.63820013245,
            -0.18073191814,
            0.14645938197,
            -11.679349811,
            null
        ],
        'X1' => [
            -686.29165263,
            61.670618028,
            2.3600125461,
            -6954.8917603,
            2602.1407871,
            25.007011066,
            29.545168928,
            28.286838744,
            -20.418135177,
            17.233836051,
            -608.12911396,
            1.75
        ],
        'X0' => [
            -10555.301065,
            1413.3516055,
            116.11356895,
            -57512.420211,
            26284.415265,
            516.7325159,
            446.10952368,
            368.72079879,
            -597.40614942,
            475.15007572,
            -10561.862035,
            0.91
        ]
    ];

    private static $preResult = [
        0 => null, // L11
        1 => null, // L12
        2 => null, // L13
        3 => null, // L14
        4 => null, // L15
        6 => null, // L17
        7 => null, // L18
        8 => null, // L19
        9 => null, // L20
        10 => null // L21
    ];

    public static function calculate($latitude, $longitude)
    {
        self::calculatePreResult($longitude, $latitude);

        $selection = [
            self::$preResult[0] == 1 ? 40 : 0,
            self::$preResult[1] == 1 && self::$preResult[0] == -1 ? 35 : 0,
            self::$preResult[2] == 1 && self::$preResult[1] == -1 ? 30 : 0,
            self::$preResult[3] == 1 && self::$preResult[2] == -1 ? 35 : 0,
            self::$preResult[4] == 1 && self::$preResult[3] == -1 && self::$preResult[1] == -1 ? 30 : 0,
            self::$preResult[6] == 1 && self::$preResult[4] == -1 ? 35 : 0,
            self::specialSelection($longitude, $latitude),
            self::$preResult[8] == 1 && self::$preResult[7] == -1 ? 45 : 0,
            self::$preResult[9] == 1 && self::$preResult[8] == -1 ? 50 : 0,
            self::$preResult[10] == 1 && self::$preResult[9] == -1 ? 45 : 0,
            self::$preResult[10] == -1 ? 50 : 0
        ];

        return max($selection);
    }

    /**
     * @param $long
     * @param $lat
     * @return int
     */
    private static function specialSelection($long, $lat)
    {
        $xi = self::$data['XI'][11];
        $x1 = self::$data['X1'][11];
        $yi = self::$data['YI'][11];
        $x0 = self::$data['X0'][11];

        if ($lat > $xi-$x1 && $lat < $xi+$x1) {
            if ($long < $yi + ((1 - ($lat - $xi) ** 2 / $x1 ** 2) * $x0 ** 2) ** 0.5
                && $long > $yi - ((1 - ($lat - $xi) ** 2 / $x1 ** 2) * $x0 ** 2) ** 0.5) {
                return 45;
            } else {
                return self::$preResult[7] == 1 && self::$preResult[6] == -1 ? 40 : 0;
            }
        } else {
            return self::$preResult[7] == 1 && self::$preResult[6] == -1 ? 40 : 0;
        }
    }

    /**
     * @param $i
     * @param $lat
     * @return float|int
     */
    private static function extraExprCheck($i, $lat) {
        $x5 = (float) self::$data['X5'][$i];
        $x4 = (float) self::$data['X4'][$i];
        $x3 = (float) self::$data['X3'][$i];
        $x2 = (float) self::$data['X2'][$i];
        $x1 = (float) self::$data['X1'][$i];
        $x0 = (float) self::$data['X0'][$i];

        return ($x5 * $lat ** 5) + ($x4 * $lat ** 4) + ($x3 * $lat ** 3) + ($x2 * $lat ** 2) + ($x1 * $lat) + $x0;
    }

    /**
     * @param $long
     * @param $lat
     * @return int
     */
    private static function specialPreResult($long, $lat)
    {
        $xi_a = (float) self::$data['XI'][4];
        $yi_a = (float) self::$data['YI'][4];
        $xf_a = (float) self::$data['XF'][4];
        $x5_a = (float) self::$data['X5'][4];
        $x4_a = (float) self::$data['X4'][4];
        $x3_a = (float) self::$data['X3'][4];
        $x2_a = (float) self::$data['X2'][4];
        $x1_a = (float) self::$data['X1'][4];
        $x0_a = (float) self::$data['X0'][4];
        $xi_b = (float) self::$data['XI'][5];
        $yi_b = (float) self::$data['YI'][5];
        $xf_b = (float) self::$data['XF'][5];
        $yf_b = (float) self::$data['YF'][5];
        $x5_b = (float) self::$data['X5'][5];
        $x4_b = (float) self::$data['X4'][5];
        $x3_b = (float) self::$data['X3'][5];
        $x2_b = (float) self::$data['X2'][5];
        $x1_b = (float) self::$data['X1'][5];
        $x0_b = (float) self::$data['X0'][5];

        if ($lat < $xi_a) {
            return $long > $yi_a ? 1 : -1;
        } else {
            if ($lat > $xf_a) {
                if ($lat < $xi_b) {
                    return $long > $yi_b? 1 : -1;
                } else {
                    if ($lat > $xf_b) {
                        return $long > $yf_b ? 1 : -1;
                    } else {
                        return ($x5_b * $lat ** 5) + ($x4_b * $lat ** 4) + ($x3_b * $lat ** 3) + ($x2_b * $lat ** 2) + ($x1_b * $lat) + $x0_b < $long ? 1 : -1;
                    }
                }
            } else {
                return ($x5_a * $lat ** 5) + ($x4_a * $lat ** 4) + ($x3_a * $lat ** 3) + ($x2_a * $lat ** 2) + ($x1_a * $lat) + $x0_a < $long ? 1 : -1;
            }
        }
    }

    /**
     * @param $long
     * @param $lat
     */
    private static function calculatePreResult($long, $lat)
    {
        array_map(function ($i) use ($long, $lat) {
            if ($i != 4) {
                if($lat < self::$data['XI'][$i]){
                    $val = $long > self::$data['YI'][$i] ? 1 : -1;
                }else{
                    if($lat > self::$data['XF'][$i]){
                        $val = $long > self::$data['YF'][$i] ? 1 : -1;
                    }else{
                        $val = self::extraExprCheck($i, $lat) < $long ? 1 : -1;
                    }
                }
                self::$preResult[$i] = $val;
            } else {
                self::$preResult[$i] = self::specialPreResult($long, $lat);
            }
        }, array_keys(self::$preResult));
    }
}

<?php

/**
 * Arquivo de testes internos
 */

include 'funcs2.php';

$phase_voltage = 380;
$n_phase = 3;

$inv = [
    [
        'phase_voltage' => 220,
        'phase_number' => 3,
        'compatibility' => 1
    ],
    [
        'phase_voltage' => 380,
        'phase_number' => 3,
        'compatibility' => 2
    ],
    [
        'phase_voltage' => 380,
        'phase_number' => 3,
        'compatibility' => 0
    ]
];

$invR = inv_phase_filter($inv, $phase_voltage, $n_phase);

var_dump($invR); die;

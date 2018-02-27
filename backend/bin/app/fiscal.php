<?php

/**
 * Execução de processos fiscais
 * Danfe e Proceda
 * Cron: {path}/cron.php fiscal proceda|danfe
 */

$currentDir = dirname(__FILE__);

require_once $currentDir . '/helpers/logger.php';

if (3 == count($argv) && in_array($argv[2], ['proceda', 'danfe'])) {

    $host = getenv('CES_SICES_HOST');

    if (8000 == $port = getenv('CES_SICES_PORT')) {
        $host .= ":{$port}";
    }

    $url = "{$host}/public/fiscal/{$argv[2]}";

    $headers = [
        'AUTHORIZATION: OewkQ42mCxVyfk7cbKg5jORFTWdWMQhxIO2bjHQt',
        'SECRET: NXTh0oqmwed4PvK3HCysMJjMWEGGJ2Fw0hXDfyox'
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);

    $result = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $content = curl_getinfo($curl);
    curl_close($curl);

    if (200 == $statusCode) {
        createLog($argv[2], 'Processo executado com sucesso.');
    } else {
        createLog($argv[2], 'Falha ao executar processamento: ' . $result, \Monolog\Logger::ERROR);
    }

    die("Processo executado, verifique o status em {$currentDir}/logs/cron-{$argv[2]}.log\n");
}

die("Processo não executado, faltam argumentos na chamada.\n");

<?php

/**
 * Execução de processos fiscais
 * Danfe e Proceda
 * Cron: {path}/cron.php fiscal proceda|danfe
 */

$currentDir = dirname(__FILE__);

require_once $currentDir . '/helpers/logger.php';

/**
 * @param $service
 * @param array $result
 * @return string
 */
function formatResultMessage($service, array $result = []){

    // Prevent undefined index exception
    $result = array_merge([
        'loaded_files' => 0,
        'loaded_events' => 0,
        'cached_events' => 0,
        'processed_files' => 0,
        'time' => 0
    ], $result);

    $formats = [
        'proceda' => [
            'message' => '%s: Processamento concluído. \n Arquivos processados: %d \n Cache \n - Inicial: %d \n - Final: %d \n Time: %s segundos',
            'data' => [strtoupper($service), $result['loaded_files'], $result['loaded_events'], $result['cached_events'], $result['time']]
        ],
        'danfe' => [
            'message' => '%s: Processamento concluído. \n Arquivos carregados: %d \n Arquivos processados: %d \n Time: %s segundos',
            'data' => [strtoupper($service), $result['loaded_files'], $result['processed_files'], $result['time']]
        ]
    ];

    return vsprintf($formats[$service]['message'], $formats[$service]['data']);
}

if (3 == count($argv) && in_array($argv[2], ['proceda', 'danfe'])) {

    $service = $argv[2];
    $host = getenv('CES_SICES_HOST');
    $port = getenv('CES_SICES_PORT');
    $label = strtoupper($service);

    $baseUri = (8000 == $port) ? "${host}:${port}" : $host;
    $uri = "/public/fiscal/{$service}";

    $headers = [
        'AUTHORIZATION' => 'OewkQ42mCxVyfk7cbKg5jORFTWdWMQhxIO2bjHQt',
        'SECRET' => 'NXTh0oqmwed4PvK3HCysMJjMWEGGJ2Fw0hXDfyox'
    ];

    $startTime = microtime(true);

    createLog($service, sprintf('%s: Processamento iniciado.', strtoupper($service)));

    $client = new \GuzzleHttp\Client([
        'base_uri' => $baseUri,
        'headers' => $headers
    ]);

    $response = $client->post($uri);

    $content = $response->getBody()->getContents();
    $statusCode = $response->getStatusCode();

    if (200 == $statusCode) {

        $result = json_decode($content, true);
        $result['time'] = round(microtime(true) - $startTime, 1);

        createLog($service, formatResultMessage($service, $result));

    } else {

        createLog($service, 'Falha ao executar processamento: ' . $content, \Monolog\Logger::ERROR);
    }

    die("Processo executado, verifique o status em {$currentDir}/logs/cron-{$service}.log\n");
}

die("Processo não executado, faltam argumentos na chamada.\n");

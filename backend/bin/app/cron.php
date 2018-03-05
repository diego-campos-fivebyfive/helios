<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/config/functions.php';

/**
 * Este arquivo é útil para execuções "cron" em nível de diretório.
 * O objetivo é que ao adicionar novas tarefas cron, ocorram apenas
 * modificações neste arquivo para que as configurações sejam centralizadas
 * mantendo o padrão de chamadas "cron".
 *
 * Path padrão de configuração do cron:
 *
 * {dirname}/cron.php {filename}
 *
 * {dirname}  : Diretório deste arquivo
 * {filename} : Arquivo "php" (sem a extensão) que será executado
 *
 * Exemplo:
 *
 * /var/www/backend/bin/app/cron account-level-handler
 *
 * Será executado o arquivo:
 * /var/www/backend/bin/app/account-level-handler.php
 */
if(count($argv) > 1){

    $script = $argv[1] . '.php';
    $filename = dirname(__FILE__) . '/' . $script;

    if(file_exists($filename)){
        require $filename;
    }
}

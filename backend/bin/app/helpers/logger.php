<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @param $context
 * @param $message
 * @param int $level
 */
function createLog($context, $message, $level = Logger::INFO){

    if (getEnvironment() === 'production') {
      exec("\$CLI_PATH/ces-slack-notify --workers '$message'");
    }

    $filename = dirname(__DIR__) . sprintf('/logs/cron-%s.log', $context);

    $log = new Logger($context);
    $log->pushHandler(new StreamHandler($filename, Logger::INFO));

    $log->addRecord($level, $message);
}

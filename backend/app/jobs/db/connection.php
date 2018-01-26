<?php

require_once 'rb.php';

$parameters = explode("\n", file_get_contents(dirname(__DIR__) . '/../config/parameters.yml'));

$config = [];
foreach ($parameters as $parameter){
    $parameter = trim($parameter);
    if(0 === strpos($parameter, 'database')){
        list($key, $value) = explode(':', $parameter);
        $config[trim($key)] = trim($value);
    }
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $config['database_host'], $config['database_port'], $config['database_name']);

R::setup($dsn, $config['database_user'], $config['database_password']);

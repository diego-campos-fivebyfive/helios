<?php

/**
 * @return array
 */
function getParameters(){
    return require_once dirname(__FILE__) . '/parameters.php';
}

/**
 * Create RedBean connection instance
 */
function connectDatabase(){

    require_once 'rb.php';

    $parameters = getParameters();

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $parameters['database_host'], $parameters['database_port'], $parameters['database_name']);

    R::setup($dsn, $parameters['database_user'], $parameters['database_password']);
}

<?php

/**
 * Autoload file
 */
function getAutoload(){
    require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
}

/**
 * @return string
 */
function getEnvironment(){

    $parameters = getParameters();

    return 'development' == $parameters['ambience'] ? 'devops' : $parameters['ambience'] ;
}

/**
 * @return array
 */
function getParameters(){

    if(!defined('PARAMETERS')){
        $parameters =  require_once dirname(__FILE__) . '/parameters.php';
        define('PARAMETERS', serialize($parameters));
    }

    return unserialize(PARAMETERS);
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

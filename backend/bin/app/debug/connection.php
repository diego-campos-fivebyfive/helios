<?php

require_once dirname(__DIR__) . '/config/connection.php';

echo R::testConnection() ? 'Connection OK' : 'Connection NOK';
echo "\n";

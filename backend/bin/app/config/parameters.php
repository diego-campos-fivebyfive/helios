<?php

use Symfony\Component\Yaml\Yaml;

$backendDir = dirname(dirname(dirname(__DIR__)));
$data = Yaml::parse(file_get_contents($backendDir . '/app/config/parameters.yml'));

return $data['parameters'];

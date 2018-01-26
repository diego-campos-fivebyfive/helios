<?php

require_once dirname(__FILE__) . '/db/connection.php';

$data = R::findOne('app_parameter', 'id = ?', ['platform_settings']);

if (!$data)  return;

$arrayParameters = json_decode($data->parameters, true);

if (!array_key_exists('account_level_handler', $arrayParameters)) return;

$config = $arrayParameters['account_level_handler'];

/**
 * @param array $config
 */
function normalizeConfig(array &$config)
{
    uasort($config['levels'], function ($a, $b) {
        return $a['amount'] > $b['amount'];
    });
}

/**
 * @param array $config
 */
function normalizeLevels(array $config)
{
    normalizeConfig($config);

    foreach ($config['levels'] as $level => $parameter) {

        $days = (int)$parameter['days'];
        $amount = (float)$parameter['amount'];

        $createdAt = (new \DateTime(sprintf('%d days ago', $days)))->format('Y-m-d');

        $updateSQL = sprintf(<<<SQL
UPDATE app_customer c
SET c.level = '%s'
WHERE (
  SELECT SUM(o.total) total
  FROM app_order o
  WHERE o.parent_id IS NULL
  AND o.status >= 7
  AND DATE(o.created_at) >= '%s' 
  AND o.account_id = c.id) > ROUND(%f, 2)
SQL
, $level, $createdAt, $amount);

        R::exec($updateSQL);
    }
}

normalizeLevels($config);

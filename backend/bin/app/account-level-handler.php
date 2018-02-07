<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Este script efetua o procedimento de alteração de nível das contas
 * com base nos orçamentos dentro dos parâmetros definidos.
 *
 * Processo:
 * 1. Efetuar o carregamento dos parâmetros de configuração
 * 2. Percorrer os níveis configurados
 * 2.1 Para cada nível, gerar a SQL correspondente a atualização, com base na configuração associada ao nível.
 * 2.2. Executar a SQL gerada
 * 3. Para bloqueio
 * 3.1 Executa uma operação de alteração para o status "LOCKED" em contas que possuam:
 * - Data de ativação < (hoje - "grace_period"),
 * - Cuja soma de orçamentos criados em data >= (hoje - "grace_period") seja inferior ao valor mais baixo configurado
 * na lista de níveis.
 */

require_once dirname(__FILE__) . '/config/connection.php';
require_once dirname(__DIR__) . '/../vendor/autoload.php';

$data = R::findOne('app_parameter', 'id = ?', ['platform_settings']);

if (!$data) return;

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
 * Log string SQL with type, year and month identity
 */
function logSQL($sql){

    $filename = dirname(__FILE__) . sprintf('/logs/cron-%s.log', date('Y-m'));

    $log = new Logger('account-level-handler');
    $log->pushHandler(new StreamHandler($filename, Logger::INFO));

    $log->info($sql);
}

/**
 * Execute SQL
 * @param $sql
 */
function executeSQL($sql)
{
    $sql .= " AND c.context = 'account' AND c.persistent = 0;";

    $sql = preg_replace('/( )+/', ' ', str_replace(["\n", "\t"], ' ', $sql));

    R::exec($sql);

    logSQL($sql);
}

/**
 * @param array $config
 */
function normalizeLevels(array $config)
{
    if(!array_key_exists('levels', $config)) {
        return false;
    }

    normalizeConfig($config);

    $levels = $config['levels'];
    $levelKeys = array_keys($levels);
    $firstLevel = $levelKeys[0];
    $gracePeriod = (int)$config['grace_period'];
    $activatedAt = (new \DateTime(sprintf('%d days ago', $gracePeriod)))->format('Y-m-d');
    $lockedStatus = 4;
    $firstAmount = (float)$config['levels'][$levelKeys[0]]['amount'];
    $firstCreatedAt = (new \DateTime(sprintf('%d days ago', $levels[$levelKeys[0]]['days'])))->format('Y-m-d');

    $firstSQL = sprintf(<<<SQL
UPDATE app_customer c
SET c.level = '%s'
WHERE c.id NOT IN (
  SELECT o.account_id
  FROM app_order o
  WHERE o.parent_id IS NULL
        AND o.status >= 7
        AND DATE(o.created_at) >= '%s'
  GROUP BY account_id
  HAVING (SUM(o.total)) >= %f
)
AND c.activated_at >= '%s'
SQL
, $firstLevel, $firstCreatedAt, $firstAmount, $activatedAt);

    $lockSQL = sprintf(<<<SQL
UPDATE app_customer c
    SET c.status = %d, c.level = '%s'
    WHERE id NOT IN (
    SELECT o.account_id
    FROM app_order o
    WHERE o.parent_id IS NULL
    AND o.status >= 7
    AND DATE(o.created_at) >= '%s'
    GROUP BY account_id
    HAVING (SUM(o.total)) >= %f
    )
    AND c.activated_at < '%s'
SQL
, $lockedStatus, $firstLevel, $firstCreatedAt, $firstAmount, $activatedAt);

    executeSQL($firstSQL);
    executeSQL($lockSQL);

    // UPGRADE / DOWNGRADE
    $index = 0;
    foreach ($levels as $level => $params) {

        $days = (int)$params['days'];
        $amount = (float)$params['amount'];
        $createdAt = (new \DateTime(sprintf('%d days ago', $days)))->format('Y-m-d');

        $expr = 0 == $index ? 'IN' : 'IN';

        $updateSQL = sprintf("UPDATE app_customer c
SET c.level = '%s'
WHERE c.id %s (
  SELECT o.account_id
  FROM app_order o
  WHERE o.parent_id IS NULL
        AND o.status >= 7
        AND DATE(o.created_at) >= '%s'
  GROUP BY account_id
  HAVING (SUM(o.total)) >= %f_FSQL_
)", $level, $expr, $createdAt, $amount);

        $updateSQL = str_replace('_ASQL_', (0 == $index) ? sprintf("AND c.activated_at >= '%s'", $activatedAt) : '' , $updateSQL);

        $index++;

        $updateSQL = str_replace('_FSQL_', $index < count($levels) ? sprintf(' AND SUM(o.total) < %f', $levels[$levelKeys[$index]]['amount']) : '', $updateSQL);

        executeSQL($updateSQL);
    }

    return true;
}

if(normalizeLevels($config)){
    echo sprintf("Levels normalized: %s", implode(', ', array_keys($config['levels'])));
}else{
    echo "Unprocessed normalization.";
}

echo "\n";

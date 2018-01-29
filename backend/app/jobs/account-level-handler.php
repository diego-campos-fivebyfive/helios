<?php

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

    // UPGRADE / DOWNGRADE

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

    // LOCK

    $levelKeys = array_keys($config['levels']);
    $lockedStatus = 4;
    $gracePeriod = (int) $config['grace_period'];
    $firstAmount = (float) $config['levels'][$levelKeys[0]]['amount'];
    $compareAt = (new \DateTime(sprintf('%d days ago', $gracePeriod)))->format('Y-m-d');

    $lockSQL = sprintf("UPDATE app_customer c
SET c.status = %d
WHERE (
    SELECT SUM(o.total) total
  FROM app_order o
  WHERE o.parent_id IS NULL
AND o.status >= 7
AND DATE(o.created_at) >= '%s'
AND o.account_id = c.id
) < ROUND(%f, 2)
AND DATE(c.activated_at) < '%s'", $lockedStatus, $compareAt, $firstAmount, $compareAt);

    R::exec($lockSQL);
}

normalizeLevels($config);

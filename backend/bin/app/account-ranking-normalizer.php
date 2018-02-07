<?php

/**
 * Este script efetua o procedimento de geração dos registros de pontuação
 * para contas com base nos orçamentos dentro dos parâmetros definidos.
 *
 * Processo:
 * 1. Excluir todos os registros de ranking
 * 2. Efetuar a busca por orçamentos com base nos parâmetros de status e potência
 * 3. Percorrer os orçamentos encontrados
 * 3.1 Para cada orçamento, gerar um registro de ranking com pontuação calculada.
 * 4. Persistir os registros de ranking
 * 5. TRIGGERS: Quando executada qualquer operação (insert/update/delete) na tabela de ranking
 *              a base de dados através do recurso de TRIGGER, atualiza instantânemente a propriedade
 *              'ranking' da conta vinculada à operação.
 */

use AppBundle\Service\Order\OrderRanking;

require_once dirname(__FILE__) . '/config/connection.php';
require_once dirname(__DIR__) . '/../vendor/autoload.php';

$mapping = OrderRanking::getMapping();

$sql = <<<SQL
SELECT 
o.id,
o.reference, 
o.level,
o.power,
o.account_id, 
o.delivery_at 
FROM app_order o 
WHERE o.parent_id IS NULL
AND o.status >= 7 
AND o.power > 0
SQL;

// Clear all ranking transactions
R::wipe('app_ranking');

$orders = R::getAll($sql);
$date = (new \DateTime())->format('Y-m-d H:i:s');
$rankings = [];

foreach ($orders as $order){

    if(!array_key_exists($order['level'], $mapping)) continue;

    $accountId = $order['account_id'];

    $target = sprintf('%s::%s', \AppBundle\Entity\Customer::class, $order['account_id']);

    $deliveryInfo = $order['delivery_at'] ? sprintf(' - Disp: %s', (new \DateTime($order['delivery_at']))->format('d/m/Y')) : '';

    $description = sprintf(
        '%s - %s%s',
        $order['reference'],
        (new \DateTime())->format('d/m/Y H:i'),
        $deliveryInfo
    );

    $amount = ceil($order['power'] * $mapping[$order['level']]);

    // create ranking
    $ranking = R::dispense('app_ranking');

    $ranking->target = $target;
    $ranking->description = $description;
    $ranking->amount = $amount;
    $ranking->created_at = $date;
    $ranking->updated_at = $date;

    $rankings[] = $ranking;
}

// Store rankings
R::storeAll($rankings);

echo sprintf("%s rankings normalized.", count($rankings));
echo "\n";

/*** TRIGGERS QUE DEVEM SER ARMAZENADAS NA BASE DE DADOS (tabela app_ranking)

DELIMITER //

DROP TRIGGER IF EXISTS onInsertRanking;
CREATE TRIGGER onInsertRanking
AFTER INSERT ON app_ranking
FOR EACH ROW
BEGIN
UPDATE app_customer c
SET c.ranking = (SELECT sum(r.amount)
FROM app_ranking r
WHERE r.target = NEW.target)
WHERE c.id = REPLACE(NEW.target, 'AppBundle\\Entity\\Customer::', '');
END//

DROP TRIGGER IF EXISTS onUpdateRanking;
CREATE TRIGGER onUpdateRanking
AFTER UPDATE ON app_ranking
FOR EACH ROW
BEGIN
UPDATE app_customer c
SET c.ranking = (SELECT sum(r.amount)
FROM app_ranking r
WHERE r.target = NEW.target)
WHERE c.id = REPLACE(NEW.target, 'AppBundle\\Entity\\Customer::', '');
END//

DROP TRIGGER IF EXISTS onDeleteRanking;
CREATE TRIGGER onDeleteRanking
AFTER DELETE ON app_ranking
FOR EACH ROW
BEGIN
UPDATE app_customer c
SET c.ranking = (SELECT sum(r.amount)
FROM app_ranking r
WHERE r.target = OLD.target)
WHERE c.id = REPLACE(OLD.target, 'AppBundle\\Entity\\Customer::', '');
END//

DELIMITER ;

 ***/

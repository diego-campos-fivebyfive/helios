<?php

require_once(dirname(__FILE__) . '/config/functions.php');
getAutoload();

/**
 * Este script servirá para transferir os dados do antigo memorial para o novo modelo.
 */


connectDatabase();

$levels = \AppBundle\Entity\Precifier\Memorial::getDefaultLevels(true);

print_r($levels);die;


$families = "SELECT DISTINCT r.family as families FROM app_precifier_range r WHERE r.memorial_id = 20";

$result = R::getAll($families);

print_r($result);die;


$sql = "SELECT COUNT(c.code) as qtt FROM app_component_inverter c";

$result = R::getAll($sql);

print_r($result);die;


// Clear all ranking transactions
//R::wipe('app_ranking');



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

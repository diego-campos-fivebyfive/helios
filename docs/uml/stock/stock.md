## Visão Geral

O mecanismo de controle de estoque efetua o gerenciamento de transações de entrada e saída de componentes.

Atualmente o serviço está ativo para controle de componentes associados aos orçamentos que passam pelos status de 
aprovação e/ou rejeição (dependendo do status de origem).

Toda transação envolvendo um dos componentes das famílias disponíveis na aplicação deve gerar como resultado
uma atualização no estoque daquele componente na base de dados.

Este processo era anteriormente executado pela aplicação.

#### Trigger

O novo modelo de controle de estoque passa a utilizar o recurso do SGBD para execução mais confiável e 
menos dependente do processo.

O objetivo principal da utilização de triggers é fornecer ao controle alguns benefícios que o modelo via 
aplicação não possibilita:

- Sincronização em tempo real dos dados de transações com a base de componentes.
- Maior confiabilidade dos dados de estoque apresentados no gerenciador.
- Maior consistência e facilidade de manutenção, por centralização do recurso.

##### Código da TRIGGER que deve ser registrada no SGBD:

```

DELIMITER //
DROP TRIGGER IF EXISTS `onInsertTransaction`;
CREATE TRIGGER `onInsertTransaction`
AFTER INSERT ON `app_stock_transaction`
FOR EACH ROW
  BEGIN
    CASE LOWER(NEW.family)
      WHEN "inverter" THEN
      UPDATE app_component_inverter c SET c.stock = c.stock + NEW.amount WHERE c.id = NEW.identity;
      WHEN "module" THEN
      UPDATE app_component_module c SET c.stock = c.stock + NEW.amount WHERE c.id = NEW.identity;
      WHEN "string_box" THEN
      UPDATE app_component_string_box c SET c.stock = c.stock + NEW.amount WHERE c.id = NEW.identity;
      WHEN "structure" THEN
      UPDATE app_component_structure c SET c.stock = c.stock + NEW.amount WHERE c.id = NEW.identity;
      WHEN "variety" THEN
      UPDATE app_component_variety c SET c.stock = c.stock + NEW.amount WHERE c.id = NEW.identity;
    END CASE;
  END//
DELIMITER ;

```

#### Processo de migração (se necessário)

Este processo deve ser executado preferencialmente antes das modificações de código da aplicação (deploy)

- Atualização da estrutura da tabela

``` 
ALTER TABLE app_stock_transaction
  ADD family VARCHAR(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  ADD identity INT DEFAULT NULL;
```

- Normalização de famílias (coluna 'family')

``` 
UPDATE app_stock_transaction SET family = 'module' WHERE product_id LIKE "%Module::%";
UPDATE app_stock_transaction SET family = 'inverter' WHERE product_id LIKE "%Inverter::%";
UPDATE app_stock_transaction SET family = 'string_box' WHERE product_id LIKE "%StringBox::%";
UPDATE app_stock_transaction SET family = 'structure' WHERE product_id LIKE "%Structure::%";
UPDATE app_stock_transaction SET family = 'variety' WHERE product_id LIKE "%Variety::%";  
```

- Normalização de identities (coluna 'identity')

``` 
UPDATE app_stock_transaction SET identity = REPLACE(product_id, 'AppBundle\\Entity\\Component\\Module::', '') WHERE family = 'module';
UPDATE app_stock_transaction SET identity = REPLACE(product_id, 'AppBundle\\Entity\\Component\\Inverter::', '') WHERE family = 'inverter';
UPDATE app_stock_transaction SET identity = REPLACE(product_id, 'AppBundle\\Entity\\Component\\StringBox::', '') WHERE family = 'string_box';
UPDATE app_stock_transaction SET identity = REPLACE(product_id, 'AppBundle\\Entity\\Component\\Structure::', '') WHERE family = 'structure';
UPDATE app_stock_transaction SET identity = REPLACE(product_id, 'AppBundle\\Entity\\Component\\Variety::', '') WHERE family = 'variety';
```

- Remoção de FOREIGN KEY e INDEX

__Obs:__ Antes de rodar os comandos abaixo, verificar os nomes corretos de
 FK e IDX na base de dados.
 
``` 
ALTER TABLE app_stock_transaction DROP FOREIGN KEY FK_CBC0E7A4584665A;
DROP INDEX IDX_CBC0E7A4584665A ON app_stock_transaction;
```

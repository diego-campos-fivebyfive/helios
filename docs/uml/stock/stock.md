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

##### Abaixo o código da TRIGGER que deve ser registrada no SGBD:

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

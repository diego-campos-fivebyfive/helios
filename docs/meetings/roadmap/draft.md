
10/10/2017 - TERCEIRO TESTE DE PROCESSO COMPLETO - ORÇAMENTOS
====================================================

:: REGRAS DO PROMOCIONAL
[295]
- Adicionar gestão de Parâmetros
-- Habilitar promocional: booleano para o gerador (checkbox). true = mostra / false = oculta
[295]
[297]
-- Frase do checkbox: "Gerar sistema PROMOCIONAL" centralizada no topo - antes dos outros campos de configuração.
- Ao selecionar a opção promocional - o form de configuração deve ser resetado com os dados compatíveis, bem como o projeto (se já tiver sido gerado).
- Apenas para Integrador - No promocional, na lista de itens ocultar as colunas "Custo unitário" e "Total", exibir apenas a qtde e o total abaixo.
-- Verificar essas regras na listagem do projeto, na edição do orçamento (modal), na lista de sistemas (Revisão), tela de visualização
-- A regra global é - se for promocional as colunas "Custo Unit" e "Total" não devem aparecer.
-- Nos emails e na proforma - vale a regra global
- O nome do orçamento deve ser sufixado com "[promo]"
- Ao editar um orçamento promocional, os itens carregados no form deve ser apenas promocionais.
*** No gerador do "Projeto" NÃO haverá promocional.
[297]

:: SICES
Listagem [292]
- A identificação do item "Frete" (ícone + texto) deve ser igual ao da visão do integrador (o texto muda conforme o tipo de frete)
- Modal: Atualizar o título conforme o tipo de frete
- OrderFinder: na propriedade $likes, alterar o campo 'o.id' para 'o.reference'
- Filtro: Apenas para ADMIN e MASTER - Seletor de usuários comercial

Wizard [291]
- Apenas ADMIN e MASTER:
-- Ao editar um markup não está atualizando o seguro.
-- Incluir acima da "Margem bruta da operação": Total de CMV e Total de Impostos (duas linhas) 

:: GERAL
Listagem [292]
- Ordenar os orçamentos de forma inversa (mais recentes primeiro)
- Incluir no topo, a informação do total de resultados encontrados (parecido com o de contas)

Wizard [291]
- No form de edição de um orçamento, estão aparecendo módulos inativos.
- Frete Sices - Processar o form de cálculo ao alterar um dos combos (estado / (interior/capital)).
- Ao definir o integrador >> Verificar no método Order::refreshCustomer():
-- Não estão sendo copiados os dados (Nome Fantasia, Razão).
-- Contato - deve ser o 'firstname' do dono da conta.
- No form do cliente, deixar os valores ao lado das labels (não embaixo).

Emails [294]
- Alterar de valor do frete >> Frete
- Adicionar TOTAL >> Total da order master
- Email VALIDATED - A palavra negociação está incorreta
- OrderMailer: Alterar o método que define o nome do integrador no envio, de getName() >> getFirstname()
- Ajustar as tabelas para promocional

Proforma [293]
- Bug visual (sobreposição) na tabela estática após o texto do "Certificado de Seguro"
- Reduzir a fonte da tabela de produtos de cada sistema
- Corrigir a palavra "TRASFERÊNCIA"
- Ajustar as tabelas para promocional


18/09/2017 - REGRAS PARA ORÇAMENTOS / PEDIDOS
=============================================

PEDIDO/ORÇAMENTO

STATUS
BUILDING:  0  Enquanto não envia para Sices
PENDING:   1  Após o envio para Sices (Já aparece para o user platform para validação)
VALIDATED: 2  Ação do user platform
APROVED:   3  Ação do user account (Ação: Enviar proforma para email do integrador)
REJECTED:  4  Ação do user account (Envia email para o integrador - Cancelado)

Loggable : Salvar as datas em que os status são modificados - KnpBehavios

Qualquer alteração efetuada em orders com status VALIDATED ou APROVED (pelo usuário integrador) - volta para BUILDING e continua exibindo para o user account

GERAÇÃO EXECUTADA POR USUÁRIO SICES
- O usuário SICES pode gerar um orçamento para um integrador, neste caso o status default é VALIDATED
- Neste caso, no gerador o usuário sices deve selecionar o integrador associado ao orçamento.

```
EDIÇÃO DE UM ORÇAMENTO
	B	P	V	A	R
I    	X		X	X	X
S		X
```

EMAILS - Destinados ao integrador (Dono da conta) 
Todos os email devem ser configurados com reply-to: conta agent (Sices) vinculado ao integrador
PENDING: Recebemos sua solicitação
VALIDATED: Validamos o seu orçamento - Aguardando aprovação
APROVED: Seu orçamento foi aprovado - Anexo proforma (pdf) - cc >> email do agent (Sices) vinculado à conta e admin (Sices)
REJECTED: Você cancelou seu orçamento

Usuário (Integrador)
Gera o orçamento e envia a solicitação para Sices

Usuário (Platform)
- Seção Orçamentos (visível para todos os users)
-- master e admin: Visualizam todos
-- agent: Visualizam orçamentos gerados por integradores vinculados a ele
- Para estes usuários, devem ser listados os pedidos (orçamentos) com a possibilidade de edição dos dados do mesmo 
(!Observar aqui as referências de vínculo platform-account nesta funcionalidade)

PENDÊNCIAS
- Textos dos emails
- Layout da proforma

WORKFLOW MACRO
1. Integrador inicia um novo orçamento
2. Integrador gera um sistema
3. Integrador configura o sistema gerado
4. Integrador adiciona sistema ao orçamento
5. Integrador envia orçamento para sices
6. Sices lista o orçamentos
7. Sices edita e/ou valida um orçamento 
8. Integrador aprova o orçamento
9. Sices gera proforma

VISUAL
Utilizar o WIZARD [inspinia](http://webapplayers.com/inspinia_admin-v2.7.1/form_wizard.html)
Etapas
1. Gerador 
1.1 Gerar o sistema (GERADOR, GRUPOS E SEGURO)
1.2 Adicionar ao orçamento (botão avançar)
1.3 Após adicionado, navega para o STEP 2
2. Lista de sistemas no orçamento
2.1 Editar orçamento (modal) ou 2.2
2.2 Adicionar mais sistemas a este orçamento ou Navegar para STEP 3
3. Review (LISTA EXPANDIDA, FRETE, TOTAL)
3.1 Enviar solicitação para Sices ou Sair

Após "Enviar" 
- Redirecionar usuário para lista de solicitações.

PROFORMA
- LAYOUT 
- CONVERSÃO PDF

PROTHEUS
- CONDIÇÕES DE PAGAMENTO
- CONVERSÃO PARA PADRÃO PLATAFORMA


02/10/2017 - ORÇAMENTO / PEDIDOS / S3 / PROTHEUS
==========================================================

:: SOLICITAÇÕES
- URGENTE: Habilitar edição de markups na edição do orçamento (MASTER e ADMIN)
--- Coluna extra (markup)
--- Tabela posterior com
Expressão
CMVs = SOMA(CMV_un * qte)
Imposto = Preço de venda TOTAL * 0.0925
Margem bruta da operação = Preço de venda TOTAL - CMVs - Imposto

--- Utilizar a faixa de potência como base para decisão sobre recarregar os ranges ou não.

- Alargar modal de edição de um orçamento
- Na lista de "Usuários Sices", ocultar o usuário "MASTER"
- Calcular na order os valores correspondentes aos percentuais da forma de pagamento
- Promocionais (após Orçamento)

:: PREVISÕES
- Nova Dashboard
- Tarefas
- Contas no formato do contato (integrador) atual





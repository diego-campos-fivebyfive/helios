
06/10/2017 - TESTE DE PROCESSO COMPLETO - ORÇAMENTOS
====================================================

:: INTEGRADOR
Menu 
[232]
- Deixar "Pedidos" sem dropdown - navega direto para lista de pedidos - Incluir botão adicionar (parecido com o de user sices)
[232]

[235]
Listagem de Orçamentos
- Incluir coluna "Proforma" com ícone "pdf" e link de visualização
- Mover as ações de "Transportadora e Comprovante" para colunas da listagem com ícone
-- Trocar o nome "Transportadora" para "Frete"
-- Quando já existir comprovante o ícone deve ter cor azul
[235]

[239]
- Remover a verificação de orçamento em edição - Ao clicar para criar um novo, gerar um novo registro.
- Criar função de exclusão de orçamentos com status BUILDING
[239]

[240]
-- Habilitar o envio de comprovante em "pdf"
-- No link de "Visualizar" alterar de download para visualização em tela.
[240]

[241]
-- Na modal "Frete" exibir os dados atuais + Endereço de entrega (campo novo)
[241]

[237]
:: SICES
Editar Orçamento (wizard)
Uma nova guia de configuração (painel) "Prazos" após a guia de "Informações do  Cliente"
- Disponibilidade: Campo de data
- Prazo: Campo de data (apenas se opção não for "Meu frete")
Abaixo do prazo, incluir o campo "Observações"
[237]

[242]
:: INTEGRADOR E SICES
- Verificar em orçamentos novos (sem sistema) está aparecendo um valor total
- Corrigir mensagem de seguro (RICO >> RISCO) na lista de itens de um orçamento/sistema
- Corrigir altura de campos na edição de grupos (baixíssima prioridade)
- Ao editar um orçamento (etapa 2 do wizard) remover o campo "Observações"
- Remover os campos de ações de status na tela de edição (trocar por um botão "Aplicar" - volta para tela de visualização) (vide: Visualizar Orçamento)
[242]

[238]
Editar Orçamento (wizard)
No painel "Frete"
- O formulário de dados da transportadora deve estar identificado com "Dados da Transportadora"
- Incluir um novo campo "Endereço de entrega" que deve aparecer nas duas opções (tanto "sices" quanto "meu frete")
- Remover a mensagem do wizard (de aprovar apenas) quando o status for VALIDATED
[238]

[234]
Visualizar Orçamento
- Após a listagem de sistemas, incluir o valor do frete (abaixo)
- No topo da página
-- Lado esquerdo (grupo de botões com: STATUS e ações de status)
-- Lado direito (botão editar "Editar Orçamento")
-- Incluir as mensagens de alerta (status VALIDATED (Verificar no editor)) no topo da página (quando aplicável)
[234]

[243]
Emails
- Caso haja seguro, incluir o valor do mesmo uma linha antes do "Valor total do Sistema" - label "Valor do Seguro"
- Caso haja frete (apenas frete sices), incluir o valor do frete abaixo do "Valor total do Sistema" - Pular duas linhas para deixar espaçado.
[243]

[244]
- Admin não recebe email CC - OrderMailer - Alterar a chamada do método setCc para addCc na linha 90
[244]

[245]
Proforma
- Item "Proposta econômica"
-- Frete - ajustar as regras
--- Quando for frete Sices - Mostrar o valor em reais do frete
--- Quando a opção for "Meu frete" - Mostrar a mensagem "Não Incluso"
--- "Incluso" ainda não existe (será ativado apenas no promocional)
- Não está exibindo as Condições de Pagamento
- Item "Disponibilidade para coleta"
-- Valor configurado no wizard
- Item "Prazo de entrega" deve exibir somente se for frete sices ou incluso (que ainda não existe)
- Item "Observações" (novo) - Deve exibir as informações digitadas no campo "Observações"

Página 5 (Bug visual): Verificar sobreposição do conteúdo no rodapé
[245]


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






18/09/2017 - REGRAS PARA ORÇAMENTOS / PEDIDOS
==========================================================

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











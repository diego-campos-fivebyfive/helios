
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
Qualquer alteração efetuada em orders com status VALIDATED ou APROVED - volta para BUILDING e continua exibindo para o user account

EDIÇÃO DE UM ORÇAMENTO
	B	P	V	A	R
I    	X		X	X	X
S		X

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

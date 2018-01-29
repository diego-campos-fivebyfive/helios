## 29/01 ##
### SEGUROS COM FAIXA DE POTÊNCIA E DE PREÇO ##
1. Fazer com que alguns seguros sejam carregados apenas quando a potência/valor do projeto esteja entre os valores mínimo e máximo configurados.
2. Podem ocorrer variações de configuração:
2.1 Configurar potência mas não preço, e vice-versa.
2.1.1 Neste caso, ignora o preço e verifica a potência.
2.2 Configurar apenas o mínimo ou o máximo de qualquer um.
2.2.1 Neste caso ignora o início ou final e considera apenas o outro valor.

## 26/01 ##
### LISTAGEM DE ORÇAMENTOS . 1402 ##
1. Verificar o botão de limpeza de datas (o primeiro não funciona, o segundo apaga os dois)
2. Alterar disposição dos campos de busca
2.1 O campo de usuário fica no lugar do campo de status atual
2.2 O campo de status vai para linha de baixo, ocupando meia página. Este campo deverá aceitar múltiplos valores (filtrar por mais de um status por vez) - Utilizar chosen (similar ao existente na dashboad).

### SEGUROS DE ORÇAMENTOS . 1403 ##
1. Para exibição do seguro, na tela de visualização e na proforma, está sendo checado com order.insurance, que retorna false caso o valor total do seguro seja zero, o ideal é que verifique apenas se existe algum seguro vinculado e não se o valor é maior que zero, pois podem haver seguros sem valor, que devem ser exibidos mesmo assim.

## 25/01 ##
### AJUSTES ##
1. PROFORMA . 1385
1.1 Alterar a data (que fica ao lado da referência) para data do status.
1.2 Se a suborder não possuir seguro, ocupa apenas uma página, se tiver seguros ocupa duas páginas, sendo a primeira com o sistema e a segunda com os seguros (Estão ocorrendo sobreposições, devido ao tamanho das descrições dos seguros)
1.2.1 Ajustar o título de cada suborder, abaixo do nome da mesma, incluir em fonte menor "Equipamentos". Na página em que serão exibidos os seguros, incluir também o título e abaixo em fonte menor "Seguros".
1.3 "Valor do Sistema", quando não houver seguro, exibe após a tabela de "Equipamentos", quando houver, exibe após a tabela de "Seguros".

2. PROPOSTA . 1387
2.1 Permitir o processamento de arquivos ".doc", atualmente permite apenas "docx".

3. EMAILS DE ORÇAMENTO . 1388
3.1 No campo "para" (não nos CC), além de enviar para o dono da conta, deve enviar também para os administradores.

4. CLONADOR DE ORÇAMENTOS . 1389
4.1 Na tela de visualização e no wizard (etapa 2), liberar essa funcionalidade para usuário comercial.

## 24/01 ##
### RANKING . 1369 ##
1. Alterar os fatores de pontuação para:
- 1.1 LEVEL_BLACK = 5
- 1.2 LEVEL_PLATINUM = 3
- 1.3 LEVEL_PREMIUM = 2
- 1.4 LEVEL_PARTNER = 1

2. Após o deploy desta modificação, o processo de normalização deve rodar novamente.

### ALTERAÇÃO AUTOMÁTICA DE NÍVEL DE CONTA - PENDENTE ###
1. Carência para bloqueio: Últimos X dias em relação a data de ativação.
2. Exemplo de configuração.
- Parceiro: 0 / Últimos 90 dias
- Premium: 500.000 / Últimos 90 dias
- Platinum: 1.000.000 / Últimos 90 dias
- Black: 2.000.000 / Últimos 90 dias
- Nível: $VALOR / $DIAS
3. Se a conta não se adequar a nenhuma configuração - a conta é bloqueada
Integrador: ativo 60 dias atrás
Para bloqueio a busca deve ser:
- Contas que tenham data de ativação < que “hoje - X dias” AND somatório(R$ orçamentos no status[>=7] < que Y)
Para cada nível:
- UPDATE Contas que tenham Somatório(orçamentos no status(>=7) and created_at(“hoje - $DIAS”)) >= $VALOR

## ETAPAS - EM ANDAMENTO ##
1. Configurar consultas SQL . 1371
2. Configurar gestão e parâmetros . 1372
3. Adequar recurso de "jobs" . ...
4. Ativar cron . ...

### DESCONTO COMERCIAL - EM ANDAMENTO ###
1. Opções de Valor fixo ou Percentual
2. As duas opções devem aparecer para ADMIN / MASTER (escolhe) - default (%)
3. Para usuário EXPANSE somente a opção "Percentual" - (oculta o seletor)
4. Quando não for ADMIN ou MASTER, o desconto em Percentual é limitado conforme o parâmetro configurado (nova configuração)

## ETAPAS ##
1. Adequar entidade Order para tratamento da nova regra (unit tests) . 1377
2. Insrerir nova configuração em Parâmetros . 1390
3. Adequar formulário/interface no Wizard, conforme a regra em "parâmetros" . 1393

### RESGATE DE PONTOS ###
1. Ainda em análise.

### SEGURO SOLAR RISCO ENGENHARIA PROJETO E INSTALAÇÃO . 1373 ###
1. Remover a linha que é exibida na listagem de itens
1.1 Wizard (modal da suborder e gerador)

---------------

## 23/01 ##
### ORDER ###
1. 1339. BUG: Após um processo de "Validação" >> "Edição (integrador)" >> "Envio para SICES", está perdendo a ref de cond. de pagamento.

### MÓDULOS RELACIONADOS ###
Funcionalidade que permite configurar nos inversores, os ids de módulos cujo inversor poderá ser elencado para o projeto.
- Exemplo:
Se no inversor A forem configurados os módulos 1, 2 e 3, ao gerar o sistema, o mecanismo deve analisar se o módulo é um dos configurados para, somente caso seja carregue tal inversor.
Caso este campo não possua valores (nulo) deve elencar de forma geral.

- 1 Mapear propriedade
- 1.1 modules
- 2 Configurar formulário.
- 2.1 Com modal, lista os módulos e marca (checkbox)
- 3 Integrar funcionalidade ao gerador.
- 3.1 Adequar consultas internas do inverter loader (PENDENTE!).

### GERADOR DE PROPOSTAS ###
1. 1333. Definir data de emissão (issuedAt) ao gerar a proposta.

### STOCK ###
1. 1332. Atualizar busca de contagens por status, usando a data do memorial como referência.

## 19/01 ##
### REVISAR REGRAS DE ORDERINVENTORY ###
1. Quando status anterior é Pendente (PENDING), transição para:
- 1.1 Cancelado (REJECTED) - Subtrai em Pendente.
- 1.2 Validado (VALIDATED) - Subtrai em Pendente e soma em Validado.
- 1.3 Editando (BUILDING) - Subtrai em Pendente.

2. Quando status anterior é Validado (VALIDATED), transição para:
- 2.1 Cancelado (REJECTED) - Subtrai em Validado.
- 2.2 Pendente (PENDING) - Subtrai em Validado e soma em Pedente.
- 2.3 Aprovado (APPROVED) - Subtrai em Validado.
- 2.4 Editando (BUILDING) - Subrai em Validado.

3. Quando o status anterior é (Editando) BUILDING, transição para:
- 3.1 Pendente (PENDING) - Soma em Pendente.
- 3.2 Validado (VALIDATED) - Soma em Validado.

### RANKING ###
1. | Adicionar filtro de texto na listagem de contas.
2. | Remover operações de edição/remoção (coluna de ações) na visão do integrador.
3. | Possibilitar ao administrador, adicionar ponto "negativos".
4. ! Embaixo do nome da conta, exibir "Saldo atual: n pontos" (Atualizar attributo da conta).
5. | Sidebar esquerda (colapsar apenas para SICES), manter expandido para integrador.
6. | Na visão Integrador, onde está "Pontuações", deixa "Saldo atual: n pontos".
7. Menu (Aguardando definição)

## 18/01 ##
### ALTERNATIVA PARA INVERSORES ###
Este campo armazenará uma relação do inversor com outro inversor, que será utilizado caso o primeiro não esteja disponível.
1. Mapear a propriedade/relacionamento
2. Ajustar consulta em InverterLoader *

## 17/01 ##
### REVISÃO TEMPLATES ###
1. Coluna da Direita
1.1 Organizar conforme o modelo fornecido no PDF.

2. Coluna da direita
2.1 Incluir texto "Visualizar Template" (remover ícone)
2.2 Incluir texto "Gerar Proposta" (remover ícone)

3. Abaixo da lista de tags
3.1 Incluir o link "Ir para o editor de propostas antigo"

4. No editor antigo
4.1 Em cima, incluir a frase em um painel "danger"
AVISO: Este modo de edição será descontinuado, recomendamos alternar para o novo método de geração de propostas
Abaixo do aviso, centralizado, incluir um botão "Alternar para o novo método"

5. Efetuar upload do novo modelo de template

## 15/01 ##
### 1 - ORÇAMENTOS . 1238 ###
1. Liberar desconto para COMMERCIAL e EXPANSE no wizard

### 2 - CONTAS . 1239 ###
1. Liberar edição de conta para usuário EXPANSE (Mesmas regras de ADMIN)

### 3 - INVERSORES . 1242, 1253 ###
1. Nova propriedade na entidade Inversor.
1.1 Novo campo no form "Potência mínima para seleção".
2. Na geração:
2.1 Caso não haja valor configurado (0 ou null),seleciona.
2.2 Caso haja, seleciona apenas quando a potência informada for maior ou igual à configurada.

### 4 - NEW FEATURE - PROGRAMA FIDELIDADE . 1250, 1252, 1255, 1257, ... ###
1. A pontuação ocorre quando o orçamento estiver no status "Coleta Disponível"
2. Regras de pontuação por nível de conta

|   |   |
|-----------|-------------|
| Black     |  3pts/kwp   |
| Platinum  |  2pts/kwp   |
| Premium   |  1pts/kwp   |
| Parceiro  |  0,5pts/kwp |
|           |             |

3. Gerar entidade para armazenar as "transações" de pontos
3.1 Data/hora, Descrição (vide: estoque), Nro pontos
4. Implementar mecanismo de gerenciamento
4.1 Replicar a interface de estoque, listando contas e suas transações.
4.2 Habilitar para ADMIN e MASTER, as operações de inserção/exclusão de transações de pontos.
5. Desenvolver normalizador para contabilizar os que já estão processados (levantar requisitos).
6. O disparo de pontuação deve ocorrer quando o orçamento for para o status "STATUS_AVAILABLE"

## 12/01 ##
### ESTOQUE ###
1. NÃO deve dar baixa apenas quando o status for: BUILDING, PENDING e VALIDATED.

### FRETE ###
1. Desativar a empresa Melhor Logística Transportes (MLT), deixar apenas a CT Botelho (CTB).
2. Configurações CTB por região
2.1 Até 50.000
2.1.1 Centro-Oeste - 4.5, Norte - 5 , Nordeste - 5, Sudeste
2.1.2 

## 10/01 ##
### WORD ###
Tabelas
- Equipamentos : Item, Quantidade
- Geração: Mês, Geração
- Caixa acumulado: Ano, Valor

2. Gerenciamento
- Será fornecido um template padrão para cada conta
- Listar os templates da conta com as ações de: Excluir, Visualizar Template (download do template), Gerar Proposta (aplica tags e download).

3. Tarefas
3.1 - Configurar processo de tags (mapeamento e replaces)
3.2 - Configurar processo de upload (integração de uploader)

### COMPROVANTES ###
1. Configurar o processo de upload de comprovantes para envio de mais de um arquivo por orçamento.
2. Ajustar o processo de download, exibir a modal com a listagem de arquivos e o link de download.

## 18/12 ##
1. Testar o comportamento, deixar um componente apenas como "finame" e ver se aparece no memorial (foi testado com módulo e não funcionou).
2. (1190, 1191) Incluir comportamento de frete incluso no "finame", com mesmo comportamento no finame.
3. (1192) Na listagem de orçamentos, exibir ao lado da quantidade total, o valor total e a potência total.

## 14/12 ##
#### TESTES ####
1. (787) ROLE FINANCING 
- 1.1 Liberar acesso às Contas (regras iguais ao FINANCIAL)
- 1.2 Liberar botão de "Confirmar Pagamento"
- 1.3 Alterar método de checagem na alteração de status de orçamento: de isPlatformMaster() para isPlatform()

2. (1175) FRETE
2.1 Opção "Frete Sices", apenas para usuário SICES, habilitar a edição do campo de valor.
2.2 Ao selecionar outro estado, o valor pode mudar, não tem problema.

3. (1176, 1177, 1178, 1179, 1180) COMPONENTES
3.1 Remover o quadro de Taxas (todos)
3.2 Remover os campos "Ativo", "Disponível para venda" e "Produto promocional" (todos)
3.3 Habilitar seletor de fabricante (estrutura)
3.4 Habilitar campo "posição" (inversores, string box, variedades)
3.5 ComponentTrait - Atualizar o método 'isPromotional' para considerar o nível e não a propriedade.

## 11/12 ##
#### FINAME ####
1. (1127) Em "Condições de Pagamento":
- 1.1. Novo campo para determinar se o mesmo é ou não Financiamento (checkbox).
- 1.2. Atualizar a listagem para exibir um ícone (check) caso o mesmo seja Financiamento.

2. (1128, 1144, 1147) Nova ROLE.
- 2.1 FINANCING : Financiamento
- 2.2 Comportamentos iguais ao do FINANCIAL
- 2.2.1 FINANCIAL
- 2.2.1.1 Vê todos na regra existente exceto financiamento.
- 2.2.2 FINANCING
- 2.2.2.1 Vê todos na regra de FINANCIAL com Financiamento.

3. (1130) Finame
- 3.1 Novo nível "finame" : "Finame"
- 3.2 Assim como "promotional" este nível não é selecionável como nível de conta.
- 3.3 Novo memorial para este nível.
- 3.4 Este nível deve aparecer nas opções de utilização na gestão "Componentes" e "Seguro" (com as outras opções).

4. (1131) Gestão de Parâmetros (Idêntico ao painel de Promocional)

5. Ativar finame
- 5.1 Abaixo do painel de "Promocional"
- 5.2 Recarrega o gerador filtrando pelo nível finame ao marcar + Reseta o projeto.
- 5.3 Não é permitido marcar ambos ao mesmo tempo.
- 5.4 Verificar ao converter se a precificação OK
- 5.5 Ajustar views e proforma

6. Atualização "finame" em componentes
- 6.1. Módulos - não aplicar
- 6.2. Inversores - todos
- 6.3. Estruturas - Apenas fabricante Sices
- 6.4. StringBox - Apenas fabricante Abb
- 6.5. Variedades - todos

7. Atualização de regras para visual
- 7.1 Para usuário integrador
- 7.1.1 No gerador, ocultar custo unit. e custo total. (igual promocional)
- 7.1.2 Nome do sistema com [finame - xxxxx] 
-- parecido com o que ocorre com o promocional
-- o código deve ser verificado na hora da geração do nome
- Verificar estes casos na edição do orçamento também.
- 7.2 Proforma
-- Nome e regras de visualização de preço.

8. BUG
8.1 Mover definição de telefone (phone) na Order para pegar da conta e não do dono.

## 27/11 ##

#### COMPONENTES ####
Ajustes nos forms de componentes (remoção de campos sem uso)

#### VALIDADE DE UM ORÇAMENTO ####
1. Cron rodando as 18:00hrs (horário de brasília)
2. Orçamento deve ter uma atributo (expireDate - DATE apenas)
3. Dias úteis para cancelamento (Configurado nos parâmetros)
4. Link dias úteis: http://www.dias-uteis.com/
5. Criar mecanismo que calcula a data de expiração
6. Liberar front para edição/cancelamento da data (comercial)
7. Exibir a frase + a data de expiração na tela de visualização (Linha da referência - lado direito).
8. Configurar serviço para cron
9. Configurar processo de edição da data por usuário comercial.

:: Validade de Orçamentos ::

|   Status  | Dias úteis |
|-----------|------------|
| APPROVED  |      3     |
| VALIDATED |      4     |


#### ESTOQUE ####
1. Regras coletadas em 23/11/2017 - Controle de estoque +
2. Liberar menu para comercial (opção + Operação bloqueada)

==========================================
Status afetados: `PENDING`, `VALIDATED`

#### Tráfego de exemplo (50) ####
```
1. BUILDING >> PENDING: PENDING: 50 (Soma em PENDING)
2. BUILDING >> VALIDATED : VALIDATED: 50 (Soma em VALIDATED)
3. PENDING >> VALIDATED: PENDING: 0, VALIDATED: 50 (Subtrai em PENDING e soma em VALIDATED)
4. PENDING >> REJECTED: PENDING: 0 (Subtrai em PENDING)
5. VALIDATED >> PENDING: PENDING: 50, VALIDATED: 0 (Subtrai em VALIDATED e soma em PENDING)
```
> OBS.: Este mecanismo está em fase de análise.

Etapas: 1014, 1015, 1016, 1017, 1018.
1. Nova propriedade previousStatus em Order
2. Nova propriedade orderInventory em Componentes
3. Atualização do processo de estoque para orçamento/componente
4. Atualização de chamadas intermediárias
5. Atualização da interface de listagem

#### NOVA ROLE - FINANCIAMENTO ####
Nova propriedade em condições de pagamento (financiamento : booleana)
Nova propriedade "financiamento" booleana em Orçamentos
Atualização do comportamento ao definir forma de pagamento (verifica se é financiamento)
Nova role de usuário e filtros de orçamento
- Quando orçamento for financiado, exibe para este e não para o financeiro
- Quando for sem financiamento, exibe para financeiro e não para financiamento.

#### ORÇAMENTOS - ISSUES OK ####

1. 968. Possibilitar deixar os campos "Disponibilidade para coleta" e "Dias após pagamento" em branco
- Quando os dois estiverem em branco, não exibe a linha de informação (visualização e pró-forma).

2. 969 . Novo campo "Validade da proposta (dias)"
- Campo para informação de número.
- Também não exibir caso esteja vazio.
- Ajustar para que os campos acima e este novo fiquem em linha única, acima de "Observações".

3. 970 . Permitir ao "Pós Venda" alterar a "Disponibilidade para coleta"
- Comportamento similar ao que ocorre com o número da NF
- Com datepicker

4. 972 . Permitir seleção de itens inativos na edição de um orçamento quando user SICES.
- Destacar a label do item inativo com cor vermelha.

5. 986. Nova coluna na listagem
- Disponibilidade para coleta

6. 987 . Novo filtro na listagem
- Disponibilidade para coleta

7. 984 . Mecanismo de clonagem de suborder

20/11/2017 - Gerardor de lista de sistemas
==========================================
Gerar um determinado número de sistemas
Cada sistema pode ser editado/excluído
Cada item do sistema pode ser editado/excluído/adicionado
Podem ser gerados sistemas promocionais, conforme as regras atuais
A lista de sistemas poderá ser exportada para CSV.

Usuários
Integrador: 
- Gerador simples, conforme o padrão
- Uma lista por conta
- Somente admin da conta pode ver

Sices:
- Uma lista por usuário

Comercial
- Pode selecionar o nível, no memorial ativo

Administrador e Master
- Pode selecionar o memorial (incluindo memorial) e o nível

CSV: Definições adiante.

Etapas:
- Desenvolvimento da interface estática (área do geraador e listagem)
- Integração com mecanismo de geração (form)
- Adequação das novas regras de geração (Quantificador, precificação, ciclos).
- Ajustes nos processos de edição.
- Exportação

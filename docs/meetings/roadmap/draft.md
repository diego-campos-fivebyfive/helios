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

### 4 - NEW FEATURE - PROGRAMA FIDELIDADE ###
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

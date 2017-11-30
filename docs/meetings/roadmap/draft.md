27/11/2017 - Seguros, Componentes e Finame

#### SEGUROS - 4 PONTOS PENDENTES ####
Propriedades: 
- type(insurance), 
- name, 
- description, 
- value, 
- target (fixo ou percentual do sistema)
- requiredLevels (níveis em que o seguro é obrigatório)
- status (status do seguro: ativo/inativo)

Em cada um deve ter a opção de forçar (requirido) conforme o nível (exibe os níveis e marca).
Comportamento similar ao seguro all risk atual (este será cadastrado futuramente).

Menu: Seguros
listagem, adição, edição, exclusão.
Aparece em:
Gerador de projetos
Edição de orçamento
Visualização (soma dos valores de seguros)
Pró-forma (listado)

1. 998 Mapear entidade.
2. 1000, 1005 CRUD + Menu.
3. Mapear associações
4. Serviço de cálculo e atualização
5. Integração com geradores
6. Atualizaçãoe interfaces

#### COMPONENTES - 3 PONTOS PENDENTES ####
Inclusão de uma nova propriedade (json) nos componentes, onde o administrador irá selecionar os níveis de desconto em que o componente estará ativo.

1. Configuração de propriedades novas (Novo paine "Regras de negócio", em cima) . 950, 962, 963, 964, 965, 966
2. Ajustes no memorial (carregamento para configuração de preços) . 967
3. Ajustes no gerador (defaults resolver e loaders) . 980, 993, 995
4. Ajustes no precificador (memorial e ranges)
5. Inserção massiva de níveis em componentes (quando liberada)
6. Ajustes nos forms de componentes (remoção de campos sem uso)

Nomes dos novos campos
Disponíveis para precificação: 
Níveis cujos componentes que aparecem no memorial para configuração de preço
Ativos no gerador: 
Níveis cujos componentes serão disponibilizados 
As opções são somente as selecionadas no anterior
Garante que não sejam selecionados níveis sem preços definidos
Labels
- Para memorial: Disponível nos níveis de desconto
- Para gerador: Disponível no gerador

#### FINAME ####
Aguardando conclusão de "COMPONENTES"

#### VALIDADE DE UM ORÇAMENTO ####
1. Cron rodando as 18:00hrs (horário de brasília)
2. Orçamento deve ter uma atributo (expireDate - DATE apenas)
3. Dias úteis para cancelamento (Configurado nos parâmetros)
4. Link dias úteis: http://www.dias-uteis.com/

:: Validade de Orçamentos ::
|   Status  | Dias úteis |
|-----------|------------|
| APPROVED  |      3     |
| VALIDATED |      4     |

5. Criar mecanismo que calcula a data de expiração
6. Liberar front para edição/cancelamento da data (comercial)

#### ESTOQUE ####
1. Regras coletadas em 23/11/2017 - Controle de estoque +
2. Liberar menu para comercial (opção + Operação bloqueada)

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

-----------------------------------------

23/11/2017 - Controle de estoque
==========================================
Status afetados: `PENDING`, `VALIDATED`

#### Tráfego de exemplo (50) ####
```
1. BUILDING >> PENDING: PENDING: 50 (Soma em PENDING)
2. VALIDATED >> PENDING: PENDING: 50, VALIDATED: 0 (Subtrai em VALIDATED e soma em PENDING)
3. PENDING >> VALIDATED: PENDING: 0, VALIDATED: 50 (Subtrai em PENDING e soma em VALIDATED)
4. PENDING >> REJECTED: PENDING: 0 (Subtrai em PENDING)
5. BUILDING >> VALIDATED : VALIDATED: 50 (Soma em VALIDATED)
```
> OBS.: Este mecanismo está em fase de análise.

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

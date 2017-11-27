27/11/2017 - Seguros, Componentes e Finame

#### SEGUROS ####
Propriedades: 
type(insurance), 
name, 
description, 
value, 
target (fixo ou percentual do sistema)

Em cada um deve ter a opção de forçar (requirido) conforme o nível (exibe os níveis e marca).
Comportamento similar ao seguro all risk atual (este será cadastrado futuramente).

Menu: Seguros
listagem, adição, edição, exclusão.
Aparece em:
Gerador de projetos
Edição de orçamento
Visualização (soma dos valores de seguros)
Pró-forma (listado)

#### COMPONENTES ####
Inclusão de uma nova propriedade (json) nos componentes, onde o administrador irá selecionar os níveis de desconto em que o componente estará ativo.

1. Configuração de propriedades novas (Novo paine "Regras de negócio", em cima)
2. Ajustes no memorial (carregamento para configuração de preços)
3. Ajustes no gerador (defaults resolver e loaders)
4. Ajustes no precificador (memorial e ranges)
5. Inserção massiva de níveis em componentes (quando liberada).
6. Ajustes nos forms de componentes (remoção de campos sem uso)

Nomes dos novos campos
Disponíveis para precificação: 
Níveis cujos componentes que aparecem no memorial para configuração de preço
Ativos no gerador: 
Níveis cujos componentes serão disponibilizados 
As opções são somente as selecionadas no anterior
Garante que não sejam selecionados níveis sem preços definidos

#### FINAME ####
Aguardando conclusão de "COMPONENTES"

### ESTOQUE ###
Regras coletadas em 23/11/2017 - Controle de estoque
 
23/11/2017 - Controle de estoque
==========================================
Status afetados: `PENDING`, `VALIDATED`

#### Tráfego de exemplo (50) ####
```
BUILDING >> PENDING: PENDING: 50
VALIDATED >> PENDING: PENDING: 50, VALIDATED: 0
PENDING >> VALIDATED: PENDING: 0, VALIDATED: 50
PENDING >> REJECTED: PENDING: 0
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

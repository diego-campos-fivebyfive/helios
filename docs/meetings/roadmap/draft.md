23/11/2017 - Controle de estoque
Status afetados: PENDING, VALIDATED

Tráfego de exemplo (50)

BUILDING >> PENDING: PENDING: 50
VALIDATED >> PENDING: PENDING: 50, VALIDATED: 0
PENDING >> VALIDATED: PENDING: 0, VALIDATED: 50
PENDING >> REJECTED: PENDING: 0

Este mecanismo está em fase de análise...

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

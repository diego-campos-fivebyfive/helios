## Entidade ##

Cupons de desconto são entidades com valor monetário atribuído, que podem ser associados 
a objetos de venda, reduzindo desta forma o valor final do objeto de venda.

Um cupom pode ou não estar associado com uma conta, ou seja, alguns cupons podem ter vínculo direto
com contas e outros não.

__Pacote__: AppBundle\Entity\Misc

__Propridades__
* code (string | not null) - Código do cupom
* name (string | not null) - Nome de identificação do cupom
* amount (float | not null) - Valor do cupom
* target (string | nullable ) - Identificação de associação do cupom, quando existir
* appliedAt (DateTime | nullable) - Data em que o target foi definido (aplicado)
* account (AccountInterface|_Customer_ | nullable) - Conta associada ao cupom

__Métodos__
* Métodos padrão (get/set), mais:
* isApplied - Retorna se a propriedade target não é nula (boolean)

__Regras__
1. Ao definir o target, a propriedade _appliedAt_ deve receber a data atual.

2. Um target deve ser formatado com uma estrutura composta pelo ClassPath (namespace + classe) da entidade alvo, mais o identificador (id)
Exemplo:
Target formatado para orçamento (Order) com id 5:  AppBundle\Entity\Order\Order::5

## Manager ##

A classe manager desta entidade estendem as funcionalidades padrão de **AbstractManager**, além de implementar alguns métodos extras:

* findByAccount(AccountInterface) - Recebe uma instância de conta e efetua a busca de cupons relacionados com a mesma.
* findByTarget(target) - Recebe a string target e efetua a busca de cupons associados ao mesmo.


## Processo de conversão ##
- A conversão é efetuada "através de um orçamento"
- Há um limite definido (percentual do valor do orçamento / parâmetros) que determina o valor máximo do cupom criado
- Incluir em parâmetros de config, o valor que será utilizado como múltiplo
- Para o integrador este é um processo de "Resgate de pontos"
- Há um valor de múltiplo, que controla os valores aplicáveis
- As opções de aplicação, são seletores e não campo de digitação
- No wizard, fica acima de "Desconto comercial", inserir janela de alerta e confirmação
- O recurso deve estar disponível também na tela de visualização
- O desconto do cupom é efetua sobre o valor final (total) do orçamento, incluindo frete
- Se houver desconto comercial, não pode resgatar pontos
- Se houver desconto comercial, desativa o cupom
- Habilitar a possibilidade de associar um cupom através do código
- Código: 6 caracteres (numeros e letras) em maiúsculo (único)
- Em caso de uso de código, cupons com valor maior que do orçamento, exibir mensagem de perda diferencial
- Processo permitido apenas para orçamento com status menor que APPROVED (3)
- Incluir na proforma e emails (Cupom de desconto) com informações

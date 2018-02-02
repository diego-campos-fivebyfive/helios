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

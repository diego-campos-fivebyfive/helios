Padrão Básico de Codificação Backend (PHP, Twig e Symfony)
============================

Este padrão é constituído pelos elementos do padrão de codificação que é 
requerido para assegurar um alto nível de interoperabilidade técnica entre 
códigos PHP compartilhados.

As palavras chave "DEVE", "NÃO DEVE", "OBRIGATÓRIO", "TEM QUE", "NÃO TEM QUE",
"DEVERIA", "NÃO DEVERIA", "RECOMENDADO", "PODE" e "OPCIONAL" existentes neste
documento devem ser interpretadas da seguinte forma:

1. "DEVE": Esta palavra, ou os termos "REQUER" ou "DEVERIA", significa
   que a definição é uma exigência absoluta da especificação.

2. "NÃO DEVE": Esta frase, ou a frase "NÃO DEVERIA", significa que a
   definição é uma proibição absoluta da especificação.

3. "PODERIA": Esta palavra, ou o adjetivo "RECOMENDÁVEL" significa que
   podem existir razões válidas em circunstâncias particulares para
   ignorar um item específico, mas todas as implicações devem ser
   compreendidas e cuidadosamente ponderadas antes de escolher um
   curso diferente.

4. "NÃO PODERIA": Esta frase, ou a frase "NÃO RECOMENDÁVEL", significa
   que podem existir razões validas em circunstâncias particulares
   em que um comportamento é aceitável ou mesmo útil, mas todas as
   implicações devem ser compreendidas e cuidadosamente poderadas
   antes de implementar qualquer comportamento descrito com essa
   rotulagem.
   
5. "PODE": Esta palavra, ou o adjetivo "OPCIONAL", significa que um item
   é realmente opcional. Um fornecedor pode optar por incluir o item
   porque um mercado em particular o requer ou porque o fornecedor sente
   que isso melhora o produto enquanto outro fornecedor pode omitir o
   mesmo item. Uma implementação que não incluir esta opção em particular
   DEVE estar preparada para interoperar com outra aplicação que incluir
   a opção, embora possivelmente com funcionalidade reduzida. No mesmo
   sentido, uma implementação que inclui a opção em particular DEVE
   estar preparada para interoperar com outra implementação que que
   não inclui a opção (exceto, é claro, para funcionalidade que a opção)
   fornece.
 
## Sumário

  1. [PHP](#php)
  1. [Twig](#twig)
  1. [Symfony](#symfony)
  1. [Referências](#referencias)

## PHP

### 1. Visão Geral:

- Nomes de classes DEVEM ser declaradas em `PascalCase`.

- Constantes de classes DEVEM ser declaradas totalmente com letras maiúsculas e
separadas por underscores.

- Nomes de métodos DEVEM ser declarados em `camelCase`.

- DEVE existir uma linha em branco após da declaração do `namespace` e DEVE
  existir uma linha em branco após o bloco de declarações de `use`.

- Chaves de abertura para classes DEVEM ser colocadas na linha seguinte e chaves
  de fechamento DEVEM ser colocadas na linha após o corpo da classe.
  
- Visibilidade DEVE ser declarada em todas as propriedades e métodos.

- Palavras-chave de estruturas de controle DEVEM ter um espaço depois delas;
  chamadas de métodos e funções NÃO DEVEM.

- Chaves de abertura para estruturas de controle DEVEM ser colocadas na mesma
  linha e chaves de fechamento DEVEM ser colocadas na linha após o corpo da
  estrutura de controle.

- Parenteses de abertura para estruturas de controle NÃO DEVEM ter um espaço
  depois delas e parenteses de fechamento para estruturas de controle NÃO DEVEM
  ter um espaço antes.
  
- Exemplo que engloba alguma das regras acima:

    ```php
    <?php
    namespace Vendor\Package;
    
    use FooInterface;
    use BarClass as Bar;
    use OtherVendor\OtherPackage\BazClass;
    
    class Foo extends Bar implements FooInterface
    {
        public function sampleFunction($a, $b = null)
        {
            if ($a === $b) {
                bar();
            } elseif ($a > $b) {
                $foo->bar($arg1);
            } else {
                BazClass::bar($arg2, $arg3);
            }
        }
    
        final public static function bar()
        {
            // corpo do método
        }
    }
    ```

### 2. Arquivos

- Arquivos DEVEM usar somente a tag `<?php`.

- Arquivos PHP DEVEM utilizar as tags 
   longas <?php ?> ou as tags de short-echo <?= ?>; 
   NÃO DEVEM utilizar mais nenhuma variação de tags.
   
- Arquivos PHP DEVEM utilizar somente UTF-8 sem BOM.

- Arquivos PHP DEVEM utilizar 4 espaços para indentação.

- NÃO existe um limite absoluto no comprimento da linha; 
O limite relativo DEVE ser de 120 caracteres; As linhas DEVERIAM ter 80 caracteres ou menos.

- Arquivos PHP DEVEM utilizar o padrão Unix LF (linefeed) de terminação
  de linhas.

- Arquivos PHP DEVEM terminar com uma única linha em branco.

- A tag de fechamento `?>` DEVE ser omitida em arquivos contendo somente PHP.
   
### 3. Constantes

- Constantes de classes DEVEM ser declaradas totalmente 
com letras maiúsculas separadas pelo underscore. Por exemplo:
     
     ```php
     <?php
     namespace Vendor\Model;
     
     class Foo
     {
         const VERSION = '1.0';
         const DATE_APPROVED = '2012-06-01';
     }
     ```
     
### 4. Propriedades (atributos)

- Este guia intencionalmente evita qualquer recomentação em relação ao uso de
  nomes de propriedades utilizando `$StudlyCaps`, `$camelCase` ou `$under_score`.
  
- Qualquer que seja a convenção de nomeação utilizada, esta DEVE ser aplicada
  consistentemente dentro de um escopo razoável. Este escopo podendo ser à nível
  de vendor, pacotes, classes ou métodos.
  
- Visibilidades DEVEM ser declaradas para todas as propriedades.

- A palavra-chave `var` NÃO DEVE ser utilizada pra declarar uma propriedade.

- NÃO DEVE haver mais de uma propriedade declarada por linha.

- Nomes de propriedades NÃO DEVEM ser prefixados com `_` para indicar
  visibilidades `protected` ou `private`.
  
- Exemplo:

    ```php
    <?php
    namespace Vendor\Package;
    
    class ClassName
    {
        public $foo = null;
    }
    ```

### 5. Métodos

- Nomes de métodos DEVEM ser declarados em `camelCase()`.

- Visibilidades DEVEM ser declaradas em todos os métodos.

- Assinaturas de métodos NÃO DEVEM ser declaradas com um espaço após o nome do
  método. 
  
- A chave de abertura DEVE ser colocada uma linha abaixo da assinatura e nomenclatura do Método e a chave de fechamento DEVE ser colocada na linha após o corpo do método. 
  
- NÃO DEVE haver um espaço depois do parenteses de abertura e NÃO DEVE haver um espaço antes do
  parenteses de fechamento.
  
- Exemplo:
    ```php
    <?php
    namespace Vendor\Package;
    
    class ClassName
    {
        public function fooBarBaz($arg1, &$arg2, $arg3 = [])
        {
            // corpo do método
        }
    }
    ```
- Quando feita uma chamada de método, NÃO DEVE haver um espaço entre
  o nome do método e o parenteses de abertura, NÃO DEVE haver um espaço após o
  parenteses de abertura e NÃO DEVE haver um espaço antes do parenteses de
  fechamento. Na lista de argumentos, NÃO DEVE haver um espaço antes de cada
  vírgula e DEVE haver um espaço após cada vírgula.
  
- Exemplo:
    ```php
    <?php
    bar();  
    $foo->bar($arg1);
    Foo::bar($arg2, $arg3);
    ``` 
- Listas de argumentos PODEM ser divididas em múltiplas linhas, onde cada linha
  subsequente é identada uma vez. Quando fazendo isto, o primeiro item da lista
  DEVE estar na linha seguinte e DEVE haver somente um argumento por linha.

- Exemplo:
    ```php
    <?php
    $foo->bar(
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    );
    ```  
    
### 6. Argumentos de Métodos

- Na lista de argumentos, NÃO DEVE haver um espaço antes de cada vírgula e DEVE
  haver um espaço após cada vírgula.

- Argumentos de métodos com valores default DEVEM ser colocados ao fim da lista de
  argumentos.
  
- Exemplo:
    ```php
    <?php
    namespace Vendor\Package;
    
    class ClassName
    {
        public function foo($arg1, $arg2, $arg3 = [])
        {
            // corpo do método
        }
    }
    ```
- Lista de argumentos PODEM ser divididas entre múltiplas linhas, onde cada linha
  subsequente é indentada uma vez. Quando feito isto, o primeiro item da lista
  DEVE estar na linha seguinte e DEVE haver somente um argumento por linha.
  
 - Exemplo:
 
    ```php
    <?php
    namespace Vendor\Package;
    
    class ClassName
    {
        public function aVeryLongMethodName(
            ClassTypeHint $arg1,
            $arg2,
            array $arg3 = []
        ) {
            // corpo do método
        }
    }
    ```

### 7. Declarações `abstract`, `final` e `static`

- Quando presentes, as declarações `abstract` e `final` DEVEM preceder as
  declarações de visibilidade.
  
- Quando presente, a declaração `static` DEVE vir depois da declaração de
  visibilidade.
  
- Exemplo:
    ```php
    <?php
    namespace Vendor\Package;
    
    abstract class ClassName
    {
        protected static $foo;
    
        abstract protected function zim();
    
        final public static function bar()
        {
            // corpo do método
        }
    }
    ```    

### 8. Palavras-chave e True/False/Null

- DEVEM ser em letras minúsculas.

- [Palavras-chave]: http://php.net/manual/en/reserved.keywords.php

### 9. Estruturas de Controle

Regras gerais:

- DEVE haver um espaço após a palavra-chave da estrutura de controle.
- NÃO DEVE haver um espaço depois do parenteses de abertura.
- NÃO DEVE haver um espaço antes do parenteses de fechamento.
- DEVE haver um espaço entre o parenteses de fechamento e a chave de abertura.
- O corpo da estrutura DEVE ser indentado uma vez.
- A chave de fechamento DEVE ser colocada na linha após o corpo da estrutura.
- O corpo de cada estrutura DEVE ser envolto por chaves. Isso padroniza como as
  estruturas se parecem e reduz a possibilidade de introduzir erros à medida que
  novas linhas são adicionadas ao corpo da estrutura.

#### 9.1 `if`, `elseif`, `else`

- Uma estrutura `if` se parece com o seguinte:

    ```php
    <?php
    if ($expr1) {
        // corpo do if
    } elseif ($expr2) {
        // corpo do elseif
    } else {
        // corpo do else
    }
    ```
- Note o posicionamento dos
  parenteses, espaços e chaves; e que `else` e `elseif` estão na mesma linha que
  a chave de fechamento do corpo da estrutura anterior.

- A palavra-chave `elseif` DEVERIA ser utilizada ao invés de `else if` para que
todas as palavras-chave de controle se pareçam com uma só palavra.
	- Nota: É preferível usar funções puras com return para resolver regras de if ao invés do uso exagerado else /elseif.
	- Exemplo:
		```php
		// Ruim
		public function menu()
		{
		  if ($user['name'] === 'foo') {
		    $hasAccess = true;
		  } elseif ($user['role'] === 'bar') {
		    $hasAccess = true;
		  } else {
		    $hasAccess = false;
		  }
		
		  if ($hasAccess) {
		    // do something
		  }
		}
		
		// Bom
		private function hasAccess($user)
		{
		  if ($user['name'] === 'foo') {
		    return true;
		  }
		
		  if ($user['role'] === 'bar') {
		    return true;
		  }
		
		  return false;
		}
		
		public function menu()
		{
		  if (self::hasAccess($user)) {
		    // do something
		  }
		}
	```

#### 9.2 `switch`, `case`

- Uma estrutura `switch` se parece com o seguinte:

    ```php
    <?php
    switch ($expr) {
        case 0:
            echo 'Primeiro case, com um break';
            break;
        case 1:
            echo 'Segundo case, passando para o próximo case';
            // sem break
        case 2:
        case 3:
        case 4:
            echo 'Terceiro case, return ao invés de break';
            return;
        default:
            echo 'Default case';
            break;
    }
    ```
- ```php
  <?php
  switch ($expr) {
      case 0:
          echo 'Primeiro case, com um break';
          break;
      case 1:
          echo 'Segundo case, passando para o próximo case';
          // sem break
      case 2:
      case 3:
      case 4:
          echo 'Terceiro case, return ao invés de break';
          return;
      default:
          echo 'Default case';
          break;
  }
  ```
- Note o posicionamento dos parenteses, espaços e chaves.

- A declaração `case` DEVE ser identada uma vez do
  `switch` e a palavra-chave `case` (ou qualquer outra palavra-chave de
  terminação) DEVE ser indentada no mesmo nível que o corpo do `case`.
  
- DEVE haver
  um comentário como `//sem break` quando a passagem próximo case é intencional
  em um corpo de `case` que não está vazio.

- Nota: É preferível o uso de object literals ao uso de switch cases.

- Exemplo:
	```php
	$expressions = [
	    0 => 'Primeiro valor',
	    1 => 'Segundo valor',
  	    2 => 'Terceiro valor'
	];

	$expression = $expressions[$expr] ?? 'Valor default';
	```

#### 9.3 `while`, `do while`

- Uma estrutura `while` se parece com o seguinte:

    ```php
    <?php
    while ($expr) {
        // corpo da estrutura
    }
    ```   
- Note o posicionamento dos parenteses, espaços e chaves.

- Similarmente, uma estrutura `do while` se parece com o seguinte:

    ```php
    <?php
    do {
        // corpo da estrutura
    } while ($expr);
    ```

#### 9.4 `for`

- Uma estrutura `for` se parece com o seguinte:

    ```php
    <?php
    for ($i = 0; $i < 10; $i++) {
        // corpo do for
    }
    ```

- Note o posicionamento dos
  parenteses, espaços e chaves.
  
#### 9.5 `foreach`

- Uma estrutura `foreach` se parece com o seguinte:

    ```php
    <?php
    foreach ($iterable as $key => $value) {
        // corpo do foreach
    }
    ```

- Note o posicionamento dos
  parenteses, espaços e chaves.
  
#### 9.6 `try`, `catch`

- Uma estrutura `try catch` se parece com o seguinte:

    ```php
    <?php
    try {
        // corpo do try
    } catch (FirstExceptionType $e) {
        // corpo do catch
    } catch (OtherExceptionType $e) {
        // corpo do catch
    }
    ```

- Note o posicionamento dos
  parenteses, espaços e chaves.
  
### 10. Closures

- Closures DEVEM ser declaradas com um espaço após a palavra-chave `function`, e
  um espaço antes e depois da palavra-chave `use`.

- A chave de abertura DEVE ser colocada na mesma linha e a chave de fechamento
DEVE ser colocada na linha seguinte ao fim do corpo da closure.

- NÃO DEVE haver um espaço após o parentese de abertura da lista de argumentos ou
variáveis e NÃO DEVE haver um espaço antes do parentese de fechamento da lista
de argumentos ou variáveis.

- Na lista de argumentos e lista de variáveis, NÃO DEVE haver um espaço antes de
cada vírgula e DEVE haver um espaço após cada vírgula.

- Argumentos de closures com valores default DEVEM ser colocados ao fim da lista
de argumentos.

- Exemplo:

    ```php
    <?php
    $closureWithArgs = function ($arg1, $arg2) {
        // corpo
    };
    
    $closureWithArgsAndVars = function ($arg1, $arg2 = true) use ($var1, $var) {
        // corpo
    };
    ``` 
    
- Listas de argumentos e variáveis PODEM ser dividas em múltiplas linhas, onde
cada linha subsequente  é indentada uma vez. Quando fazendo isto, o primeiro
item da lista DEVE estar na próxima linha e DEVE haver somente um argumento ou
variável por linha.

- Quando uma lista finalizando (sendo argumentos ou variáveis) é divida em
múltiplas linhas, o parentese de fechamento e a chave abertura  DEVEM ser
colocados em sua própria linha com um espaço entre eles.

- A seguir estão exemplos de closures com e sem listas de argumentos e variáveis
que se dividem por múltiplas linhas.

    ```php
    <?php
    $longArgsNoVars = function (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) {
       // corpo
    };
    
    $noArgsLongVars = function () use (
        $longVar1,
        $longerVar2,
        $muchLongerVar3
    ) {
       // corpo
    };
    
    $longArgsLongVars = function (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) use (
        $longVar1,
        $longerVar2,
        $muchLongerVar3
    ) {
       // corpo
    };
    
    $longArgsShortVars = function (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) use ($var1) {
       // corpo
    };
    
    $shortArgsLongVars = function ($arg) use (
        $longVar1,
        $longerVar2,
        $muchLongerVar3
    ) {
       // corpo
    };
    ```

- Note que as regras de formatação também se aplicam em closures que são
utilizadas diretamente numa chamada de função ou método como um argumento.

```php
<?php
$foo->bar(
    $arg1,
    function ($arg2) use ($var1) {
        // corpo
    },
    $arg3
);
```

- Nota: É aconselhavel retirar argumentos multiline de closures em variáveis para clarificar a leitura.

## Twig

- Delimitadores DEVEM ser declarados com somente um espaço depois
do inicio e fim de sua declaração.

- Exemplo:
    ```twig
    {{ foo }}
    {# comment #}
    {% if foo %}{% endif %}
    ```
- DEVE ser colocado somente um espaço antes e depois dos operadores
de comparação (==, !=, <, >, >=, <=), operadores matemáticos(+, -, /, *, %, //, *),
operadores lógicos(not, and, or), ~, is, in e o operador ternário (?:).

- Exemplo:
    ```twig
    {{ 1 + 2 }}
    {{ foo ~ bar }}
    {{ true ? true : false }}
    ```    

- DEVE ser colocado somente um espaço depois de ":" em hashes e "," em
arrays(vetores).

- Exemplo:
    ```twig
    {{ [1, 2, 3] }}
    {{ {'foo': 'bar'} }}
    ``` 

- NÃO DEVE ser colocado nenhum espaço antes e depois da abertura e do fechamento de parênteses
em expressões.

- Exemplo:
    ```twig
    {{ 1 + (2 * 3) }}
    ```

- NÃO DEVE ser colocado nenhum espaço antes e depois de delimitadores de string.

- Exemplo:
    ```twig
    {{ 'foo' }}
    {{ "foo" }}
    ```    

- NÃO DEVE ser colocado nenhum espaço antes e depois dos seguintes operadores: |, ., .., []:

- Exemplo:
    ```twig
    {{ foo|upper|lower }}
    {{ user.name }}
    {{ user[name] }}
    {% for i in 1..12 %}{% endfor %}
    ```   

- NÃO DEVE ser colocado nenhum espaço antes e depois que parênteses forem usados para
filtros e/ou chamadas de métodos

- Exemplo:
    ```twig
    {{ foo|default('foo') }}
    {{ range(1..10) }}
    ``` 

- NÃO DEVE ser colocado nenhum espaço antes e depois do fechamento de arrays(vetores) e hashes.

- Exemplo:
    ```twig
    {{ [1, 2, 3] }}
    {{ {'foo': 'bar'} }}
    ``` 

- A declaração de variáveis DEVE ser feita em minúsculo e/ou com underline.

- Exemplo:
    ```twig
    {% set foo = 'foo' %}
    {% set foo_bar = 'foo' %}
    ``` 

- Blocos de código aninhados DEVEM ser indentados.

- Exemplo:
    ```twig
    {% block foo %}
        {% if true %}
            true
        {% endif %}
    {% endblock %}
    ``` 
        
## Symfony (2.8)

### 1. Estrutura

- DEVE ser colocado um único espaço em branco após cada delimitador vírgula (,).

- DEVE ser colocado um único espaço em torno de operadores binários (==, &&, ...), com exceção do operador de concatenação (.)

- Operadores unários (!, -, ...)  DEVEM ser colocados adjacentes à variável afetada.

- DEVE ser usado o operador de comparação (===) ao fazer comparações a não ser que seja necessário a coerçao de tipos.

- DEVE ser colocado vírgula após cada item num array(vetor) de múltiplas linhas.

- DEVE ser colocado uma linha em branco antes de `return` a não ser que seja o único comando dentro de um bloco de código.

- DEVE ser usado `return null;` quando um método retornar nulo explicitamente.
retornar valores `void`.

- DEVEM ser usados chaves no corpo de estruturas de controle independente do número de comandos que contenha.

- DEVE ser definida apenas uma classe por arquivo (Não se aplica para classes helper privadas que não serão instanciadas externamente).

- DEVE ser declarada a herança e as interfaces implementadas por uma classe na mesma linha de declaração do nome da classe.

- DEVEM ser declarados as propriedades(atributos) de uma classe antes dos métodos.

- Os métodos DEVEM ser declarados na ordem: `public`, `protected` e `private`.
    - Nota 1: Métodos mágicos(ex: __call, __construct), são independentes e DEVEM ficar no topo após a declaração de propriedades(atributos) de classe.
    - Nota 2: Os métodos (menos os métodos mágicos) DEVEM ser declarados na ordem:
        - Métodos abstratos {visibilidade}.
        - Métodos {visibilidade}.
        - Métodos estáticos {visibilidade}.
        - Métodos finais {visibilidade}.

- Resumindo a ordem padrão de declaração em classes é:
    - Propriedades(atributos)
    - Métodos mágicos
    - Métodos abstratos {visibilidade}
    - Métodos {visibilidade}
    - Métodos estáticos {visibilidade}
    - Métodos finais {visibilidade}


- Os argumentos de um método DEVEM ser declarados na mesma linha de declaração do nome do método independente do número de argumentos. 

- Mensagens de `Exception` e `Error` devem ser concatenadas usando `sprintf`.

### 2. Convenções de Nomenclatura

- DEVE ser usado camelCase na nomenclatura de variáveis, funções, métodos e argumentos de métodos.

- DEVE ser usado underline para a configuração de `options` e `parameters`.

- DEVE ser usado namespace na nomenclatura de todas as classes.

- DEVE ser prefixado `Àbstract` na nomenclatura de classes abstratas.

- DEVE ser sufixado `Interface` na nomenclatura de Interfaces.

- DEVE ser sufixado `Exception` na nomenclatura de Exceptions.

- DEVE ser usado caracteres alfa-numéricos e underlines na nomenclatura de arquivos.

- DEVE ser usado `bool` (ao invés de `boolean` ou `Boolean`) para type-hinting no PHPDocs e casting,
int (ao invés de `integer`) e float (ao invés de `double` ou `real`)

### 3. Nomenclatura de Serviços

- Um serviço DEVE conter grupos separados por pontos.

- A DI alias da bundle é o primeiro grupo.

- DEVE ser usado caracteres minúsculos na nomenclatura de parametros.

- DEVE ser usado a notação de underline na nomenclatura de um grupo.

### 4. Documentação

- DEVE ser adicionado PHPDoc para todas as classes, métodos e variáveis se adicionarem algum valor de entendimento.

- As anotações DEVEM ser agrupadas por tipo e anotações de tipos diferente DEVEM ser separadas por uma linha em branco.

- `@return` DEVE ser omitido se o método não retona nada.

- As anotações `@package` e `@subpackage` não DEVEM ser usadas.

- Blocos PHPDoc NÃO DEVEM ser deixados inline mesmo que contenham apenas uma tag.

### 5. Exemplo

- Exemplo curto contemplando todos os padrões básicos Symfony (2.8):

    ```php
    <?php
    
    /*
     * This file is part of the Symfony package.
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */
    
    namespace Acme;
    
    /**
     * Coding standards demonstration.
     */
    class FooBar
    {
        const SOME_CONST = 42;
    
        /**
         * @var string
         */
        private $fooBar;
    
        /**
         * @param string $dummy Some argument description
         */
        public function __construct($dummy)
        {
            $this->fooBar = $this->transformText($dummy);
        }
    
        /**
         * @return string
         *
         * @deprecated
         */
        public function someDeprecatedMethod()
        {
            @trigger_error(sprintf('The %s() method is deprecated since version 2.8 and will be removed in 3.0. Use Acme\Baz::someMethod() instead.', __METHOD__), E_USER_DEPRECATED);
    
            return Baz::someMethod();
        }
    
        /**
         * Transforms the input given as first argument.
         *
         * @param bool|string $dummy   Some argument description
         * @param array       $options An options collection to be used within the transformation
         *
         * @return string|null The transformed input
         *
         * @throws \RuntimeException When an invalid option is provided
         */
        private function transformText($dummy, array $options = array())
        {
            $defaultOptions = array(
                'some_default' => 'values',
                'another_default' => 'more values',
            );
    
            foreach ($options as $option) {
                if (!in_array($option, $defaultOptions)) {
                    throw new \RuntimeException(sprintf('Unrecognized option "%s"', $option));
                }
            }
    
            $mergedOptions = array_merge(
                $defaultOptions,
                $options
            );
    
            if (true === $dummy) {
                return null;
            }
    
            if ('string' === $dummy) {
                if ('values' === $mergedOptions['some_default']) {
                    return substr($dummy, 0, 5);
                }
    
                return ucwords($dummy);
            }
        }
    
        /**
         * Performs some basic check for a given value.
         *
         * @param mixed $value     Some value to check against
         * @param bool  $theSwitch Some switch to control the method's flow
         *
         * @return bool|void The resultant check if $theSwitch isn't false, void otherwise
         */
        private function reverseBoolean($value = null, $theSwitch = false)
        {
            if (!$theSwitch) {
                return;
            }
    
            return !$value;
        }
    }
    ```  
 
## Referências

- [PSR-0](https://www.php-fig.org/psr/psr-0/)
- [PSR-1](https://www.php-fig.org/psr/psr-1/)
- [PSR-2](https://www.php-fig.org/psr/psr-2/)
- [Twig Coding Standards](https://twig.symfony.com/doc/2.x/coding_standards.html)
- [Symfony 2.8 Coding Standards](https://symfony.com/doc/2.8/contributing/code/standards.html)

## Links Úteis

- [PHP Linter](http://cs.sensiolabs.org/)
- [Twig Linter](https://github.com/asm89/twig-lint)
- [PHP Clean Code](https://github.com/jupeter/clean-code-php)
- [JavaScript Clean Code](https://github.com/felipe-augusto/clean-code-javascript)
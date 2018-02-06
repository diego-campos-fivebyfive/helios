## Parser ##

Classe responsável pela extração/análise dos dados contidos em um arquivo PROCEDA (OCOREN).
Efetua o tratamento de eventos com base em caminho de arquivo, conteúdo ou array.

__Pacote__: App\Proceda

__Métodos__
* fromArray: Recebe como argumento um array contendo registros aleatórios e efetua a extração.
* fromContent: Recebe como argumento o conteúdo a ser analisado efetua a conversão para array e utiliza o método fromArray para extração.
* fromFile: Recebe como argumento o caminho completo do arquivo e efetua a leitura de seu conteúdo e utiliza o método fromContent para extração.

__Output (exemplo)__

```
// Cada ocorrência contida no arquivo deve gerar uma estrutura similar a esta.
// A saída normalmente é uma coleção destas ocorrências.

[
    'code' => 542,                  // IDENTIFICADOR DE REGISTRO
    'document' => 17302990000115,   // CNPJ (CGC) DO EMISSOR DA NOTA FISCAL
    'serial' => 115,                // SÉRIE DA NOTA FISCAL
    'invoice' => 000002529,         // NÚMERO DA NOTA FISCAL
    'event' => 001,                 // CÓDIGO DE OCORRÊNCIA NA ENTREGA
    'date' => 24012018,             // DATA DA OCORRÊNCIA
    'time' => 0828                  // HORA DA OCORRÊNCIA
]

```

__Regras__
2. Atualmente farão parte dos dados extraídos apenas eventos cujo registro de evento (register) seja 542.

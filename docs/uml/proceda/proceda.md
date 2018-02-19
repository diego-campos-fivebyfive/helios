### Visão Geral

O processo de notificação de ocorrências entre as Transportadoras x Sices, gera um arquivo txt 
cuja estrutura é explicada abaixo:
- 1 - Cada arquivo é prefixado com a string "OCOREN".
- 2 - O conteúdo de cada arquivo é uma coleção de eventos estrurados conforme o padrão de documentos
PROCEDA/OCOREN, este padrão pode ser verificado na documentação "OCOREN-50.pdf".
- 3 - No topo do arquivo ficam os cabeçalhos:
  - 3.1 Intercâmbio: Com informações de identificação textual do remetente(transportadora) e destinatário.
  - 3.2 Documento: Com informações sobre a utilidade do documento.
- 4 - Após os blocos de cabeçalho, são listadas as ocorrências, cada ocorrência composta por dados
estruturados conforme o padrão presente na página 3 da documentação (última tabela).

## Ciclo de Processamento ##

O objetivo principal do processamento das ocorrências de entrega é - alterar o status de um orçamento
e gerar um registro de timeline - para cada registro de ocorrência encontrado.

__Para o processamento das ocorrências de entrega, são necessários os seguintes passos:__

- 1 - Conectar o diretório correspondente via FTP.
- 2 - Efetuar a leitura (listagem) de arquivos prefixados com "OCOREN".
- 3 - Efetuar o parse de cada arquivo da lista [Parse de arquivo](#parse-de-arquivo). 
- 4 - Processar o conteúdo de cada evento extraído [Processar Evento](#processar-evento).
- 5 - Prefixar cada arquivo após o processamento [Prefixar Arquivo](#prefixar-arquivo).

__Parse de Arquivo__

- 1 - Efetuar a leitura de seu conteúdo.
- 2 - Extrair a identificação do cabeçalho de intercâmbio.
- 3 - Extrair os dados de cada evento cujo campo IDENTIFICADOR DE REGISTRO possua valor "542".
- 4 - Formatar os dados de cada evento conforme o padrão disponível na seção [Padrão de Evento](#padrao-de-evento).

O resultado deste processo é um array contendo uma chave __header__ - contendo aos dados do cabeçalho -
e uma chave __events__ - contendo uma coleção de eventos.


__Padrão de Evento__

Cada ocorrência contida no arquivo deve gerar uma estrutura similar a esta.

```
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

__Processar Evento__

O processamento de cada evento extraído, é composto pelas seguintes operações:

1 - Alterar o status do orçamento

Regras de status conforme o código:

- Código 000 - Status DELIVERING = 9
- Códigos 001, 002, 031, 150 - Status DELIVERED - 10

2 - Gerar um registro de timeline

Por hora, o padrão do conteúdo da timeline ainda não está formatado, gerar com dados simulados.

**
A operação de processamento do evento considera que o orçamento foi encontrado pelo número da NF 
presente na estrutura do evento, caso o orçamento não seja encontrado, é necessário armazenar o evento
em cache, para processamento posterior.

__Prefixar Arquivo__

Este procedimento consiste em alterar o nome do arquivo no diretório FTP, de forma a evitar que este seja
reprocessado em operações futuras.

Formato do prefixo: __PROCESSED-YYYYMMDD__ (YYYYMMDD - AnoMesDia)

##### POR HORA, AS CONSIDERAÇÕES ABAIXO DEVEM SER IGNORADAS 

## Mudanças em usuários SICES ##

__Pós-Venda__:

Atualmente executa: COLLECTED >> DELIVERED. Esta ação será removida.
Quem executará este processo é o serviço PROCEDA.

## Parser ##

Classe responsável pela extração/análise dos dados contidos em um arquivo PROCEDA (OCOREN).
Efetua o tratamento de eventos com base em caminho de arquivo, conteúdo ou array.

__Pacote__: App\Proceda

__Métodos__
* fromArray: Recebe como argumento um array contendo registros aleatórios e efetua a extração.
* fromContent: Recebe como argumento o conteúdo a ser analisado efetua a conversão para array e utiliza o método fromArray para extração.
* fromFile: Recebe como argumento o caminho completo do arquivo e efetua a leitura de seu conteúdo e utiliza o método fromContent para extração.



__Regras__
2. Atualmente farão parte dos dados extraídos apenas eventos cujo registro de evento (register) seja 542.

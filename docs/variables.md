Variaveis de Ambiente
=====================

Padrão de configuração de variaveis do sistema Sices Solar.

pasta /devops/cli/config

Regras:

Para variaveis usadas por diversos ambientes:

  Devem ser instanciadas na raiz da pasta dentro do arquivo variables, usando como padrão o nome da aplicação, variante, e nome da variavel.
  ex.:

  SICES_MAILER_HOST

  Aplicação: Sices
  Variantes: Mailer
  Variavel: Host

Para variaveis usadas exclusivamente em um ambiente:

  Devem ser instanciadas dentro da pasta do ambiente no arquivo variables, o nome da aplicação, ambiente, variante, e nome da variavel.
  ex.:

  SICES_HOMOLOG_DATABASE_HOST

  Aplicação: Sices
  Variantes: Database
  Variavel: Host

  Devem ser exportadas no arquivo variables-ci, usando como padrão o sufixo CES, o nome da aplicação, variante, e nome da variavel.
  ex.:

  CES_SICES_DATABASE_HOST

  Aplicação: Sices
  Variantes: Database
  Variavel: Host

As variaveis usadas pra o synfony serão inseridas pelo bash util variables-replace nos arquivos de config quando os mesmos usarem o nome da variable.

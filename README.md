SICES SOLAR
===========

Sistema de suporte para empresas do setor de energia solar fotovoltaíca.

> **A Plataforma**: [https://app.plataformasicessolar.com.br](https://app.plataformasicessolar.com.br)

## Sumário

  1. [Execução de Tarefas](#tarefas)
  1. [Workflow](#workflow)
  1. [Instalação do Sistema](#instalacao)
  1. [Comandos do Sices](#comandos)
  1. [Padrões de Desenvolvimento](#padrões-de-desenvolvimento)
  1. [Sobre](#sobre)

## Workflow

  <a name="workflow--ferramentas"></a><a name="1.1"></a>
  - [1.1](#workflow--ferramentas) **Ferramentas**:

    - [Waffle](https://waffle.io/sices/sices/join): Gerenciamento de tarefas (issues)
    - [Trello](https://trello.com/b/jA3wpbqG/sices-roadmap): Acompanhamento de roadmap (cards)
    - [Github](https://github.com/sices/sices): Versionamento
    - [Slack](https://kolinalabs-si.slack.com/messages): Chat e bots
    - [Hangout](https://hangouts.google.com/?hl=pt-BR): Calls

  <a name="workflow--flags"></a><a name="1.2"></a>
  - [1.2](#workflow--flags) **Slack - Solicitações**: utilizamos por padrão flags de classificações no inicio de cada solicitação.

    - HELP: para solicitar ajuda/pair (chat [developers](https://kolinalabs-si.slack.com/messages/C6AS6KEK1))
    - REVIEW: para solicitar review (chat [developers](https://kolinalabs-si.slack.com/messages/C6AS6KEK1))
    - TEST: para solicitar test (chat [tester](https://kolinalabs-si.slack.com/messages/C63Q7FKBN))

    `Ex.: @here, REVIEW: https://github.com/sices/sices/pull/0000`

    Para responder uma solicitação utilizamos por padrão o nome de usuário junto a resposta.

    `Ex.: @mauroandre [MESSAGE]`

    > **Nota**: Para respostas curtas de confirmação pode ser utilizado apenas `:+1:`


**[⬆ Voltar ao Topo](#sumário)**

## Padrões de Desenvolvimento

  <a name="padroes-desenvolvimento--variaveis-ambiente"></a><a name="1.1"></a>
  - [1.1](#padroes-desenvolvimento--variaveis-ambiente) **Definição de Variaveis de Ambiente**:

    > devops/cli/config/

    #### Regras Gerais:

    - Variaveis usadas por vários ambientes devem ser definidas no arquivo `variables` usando como padrão o nome da aplicação, variante, e nome da variavel.
      ```
      Ex.:

      SICES_MAILER_HOST

      Aplicação: Sices
      Variantes: Mailer
      Variavel: Host
      ```

    - Variaveis usadas exclusivamente em um ambiente devem ser definidas no arquivo `variables` dentro da pasta de seu respectivo ambiente, usando como padrão o nome da aplicação, ambiente, variante, e nome da variavel.
      ```
      Ex.:

      SICES_HOMOLOG_DATABASE_HOST

      Aplicação: Sices
      Ambiente: Homolog
      Variantes: Database
      Variavel: Host
      ```

    - Variaveis definidas exclusivamente para um ambiente devem ser exportadas no arquivo variables-ci com nome generico, de maneira a tornar-sem dinamicas conforme mudança de ambiente. Utilizamos como padrão o prefixo CES no inicio, o nome da aplicação, variante, e nome da variavel.
      ```
      Ex.:

      CES_SICES_DATABASE_HOST

      Aplicação: Sices
      Variantes: Database
      Variavel: Host
      ```

    > **Nota**: As variaveis usadas pelo Symfony são inseridas automaticamente no arquivo `parameters.yml` ao rodar o comando ces-app-config. Variaveis utilizadas pelo Symfony devem ter seu sample definido nos arquivos `backend/app/config/parameters_sample.yml` e `devops/cli/util/variables-replace`.


**[⬆ Voltar ao Topo](#sumário)**

## Sobre

  <a name="sobre--equipe"></a><a name="1.1"></a>
  - [1.1](#sobre--equipe) **A equipe**:

    - #### Alisson Alves
    ```
    Full-stack developer
    Slack: @alissonalmachado
    Github: @alissonam
    E-mail: alissonalmachado@gmail.com
    ```

    - #### Claudinei Machado
    ```
    Full-stack developer
    Slack: @cjchamado
    Github: @cjchamado
    E-mail: cjchamado@gmail.com
    ```

    - #### Fabio Dukievicz
    ```
    Full-stack developer
    Slack: @fabiojd47
    Github: @kascat
    E-mail: fabiojd47@gmail.com
    ```

    - #### João Zaqueu Chereta
    ```
    Full-stack developer
    Slack: @joaozaqueuchereta
    Github: @joaozaqueu
    E-mail: joaozaqueuchereta@gmail.com
    ```

    - #### Marcelo Marco
    ```
    AWS enginner
    Slack: @marcelomarco1981
    Github: @marcelomarco
    E-mail: marcelomarco1981@gmail.com
    ```

    - #### Mauro André
    ```
    Product enginner
    Slack: @mauroandre
    Github: @mauroandre
    E-mail: eng.mauroandre@gmail.com
    ```

    - #### Rafael Kendrik
    ```
    Full-stack developer
    Slack: @rafaelkendrik
    Github: @rafamikovski
    E-mail: rafamikovski@hotmail.com
    ```


**[⬆ Voltar ao Topo](#sumário)**

@ Sices Solar 2017

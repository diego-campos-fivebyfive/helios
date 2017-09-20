SICES SOLAR
===========

Sistema de suporte para empresas do setor de energia solar fotovoltaíca.

##### [https://app.plataformasicessolar.com.br](https://app.plataformasicessolar.com.br)

## Sumário

  1. [Guia Geral](#guia-geral)
  1. [Workflow](#workflow)
  1. [Instalações da Aplicação](#instalações-da-aplicação)
  1. [Padrões de Desenvolvimento](#padrões-de-desenvolvimento)
  1. [Execução de Tarefas](#execução-de-tarefas)
  1. [Lista de Comandos](#comandos)
  1. [Sobre](#sobre)

## Guia Geral

Esclarecimentos gerais relacionados a documentação:

  <a name="guia--nomenclaturas"></a><a name="1.1"></a>
  - [1.1](#guia--nomenclaturas) **Nomenclaturas**:

    - Cards de Roadmap: fragmentos de escopos do produto
    - Issues: tarefas

  <a name="guia--siglas"></a><a name="1.2"></a>
  - [1.2](#guia--siglas) **Siglas**:

    - PR: Pull Request

  <a name="guia--notas"></a><a name="1.3"></a>
  - [1.3](#guia--notas) **Notas Gerais**:

    - Em comandos, os colchetes `[]` delimitam que alguns conteúdos devem ser preenchidos em seu lugar;
    - A distro Ubuntu 16.04 foi utilizada como base de referência para a elaboração desta documentação, em outras distribuições podem ocorrer pequenas variações.


**[⬆ Voltar ao Topo](#sumário)**

## Workflow

  <a name="workflow--ferramentas"></a><a name="2.1"></a>
  - [2.1](#workflow--ferramentas) **Ferramentas**:

    - [Waffle](https://waffle.io/sices/sices/join): Gerenciamento de tarefas (issues)
    - [Trello](https://trello.com/b/jA3wpbqG/sices-roadmap): Acompanhamento de roadmap (cards)
    - [Github](https://github.com/sices/sices): Versionamento
    - [Slack](https://kolinalabs-si.slack.com/messages): Chat e bots
    - [Hangout](https://hangouts.google.com/?hl=pt-BR): Calls

  <a name="workflow--tarefas"></a><a name="2.2"></a>
  - [2.2](#workflow--tarefas) **Ciclo de vida de Tarefas**:

    - Para cada tarefa há um prazo máximo de execução de 2 dias;
    - Caso a execução da tarefa fique travada em mais de 20min deve ser solicitada ajuda utilizando a flag `HELP`;
    - Caso a execução de uma tarefa ultrapasse 2 dias a mesma deve ser reavaliada pela a equipe;
    - Tarefas devem ser quebradas em caso de: tarefas muito grandes, tarefas que modifiquem diversas partes diferentes do projeto ou caso a execução da tarefa ultrapasse os 2 dias;

      ##### A execução de tarefas segue o seguinte fluxo:
        ```
        1. Iniciada em Backlogs (general, devops)
        2. Incluida em Sprint Semanal (to do)
        3. Executada pelo Desenvolvedor (in progress) ou
           Apontada como execução impossibilitada (blocked)
        4. Enviada para Revisão de código (review)
        5. Enviada para Teste em Homolog (testing)
        6. Marcada como concluida (done)
        ```

  <a name="workflow--review"></a><a name="2.3"></a>
  - [2.3](#workflow--review) **Revisão de Pull Request**:

    - As revisões de Pull Request devem ser feitas exclusivamente através do Github;
    - Comentários devem ser feitos na Pull Request e avisados via Slack;
    - É proibido realizar merge de Pull Request sem responder aos comentários;

  <a name="workflow--flags"></a><a name="2.4"></a>
  - [2.4](#workflow--flags) **Solicitações no Slack**: utilizamos por padrão flags de classificações no inicio de cada solicitação.

    - **HELP**: para solicitar ajuda/pair (chat [developers](https://kolinalabs-si.slack.com/messages/C6AS6KEK1))
    - **REVIEW**: para solicitar review (chat [developers](https://kolinalabs-si.slack.com/messages/C6AS6KEK1))
    - **TEST**: para solicitar test (chat [tester](https://kolinalabs-si.slack.com/messages/C63Q7FKBN))

    `Ex.: @here, REVIEW: https://github.com/sices/sices/pull/0000`

    Para responder uma solicitação utilizamos por padrão o nome de usuário junto a resposta.

    `Ex.: @mauroandre [MESSAGE]`

    > **Nota**: Para respostas curtas de confirmação pode ser utilizado apenas `:+1:`


**[⬆ Voltar ao Topo](#sumário)**

## Instalações da Aplicação

  <a name="aplicacao--git"></a><a name="3.1"></a>
  - [3.1](#aplicacao--git) **Git e Github**:

    - ##### 3.1.1. *Instalando o Git*
    ```
    $ sudo apt install git
    ```

    - ##### 3.1.2. *Configurando informações do Git*
    ```
    $ git config --global user.email "mail@mail.com"
    $ git config --global user.name "Full Name"
    ```

    - ##### 3.1.3. *Criando chave para acesso SSH*
    ```
    $ ssh-keygen -t rsa -b 4096 -C "mail@mail.com"
    $ cat ~/.ssh/id_rsa.pub
    ```

    - ##### 3.1.4. *Inserindo chave SSH no Github*
    ```
    https://help.github.com/articles/adding-a-new-ssh-key-to-your-github-account/
    ```

    - ##### 3.1.5. *Clonando o repositório do Github*
    ```
    $ git clone git@github.com:sices/sices.git
    ```

  <a name="aplicacao--bash"></a><a name="3.2"></a>
  - [3.2](#aplicacao--bash) **Bashrc**:

    - ##### 3.2.1. *Abra o Arquivo `.bashrc` com seu editor (Vim, Nano ou outro)*
    ```
    $ sudo vim ~/.bashrc
    ```

    - ##### 3.2.2. *Adicione ao final do arquivo as linhas*
    ```
    export SICES_PATH=[PROJECT_PATH]
    source $SICES_PATH/devops/cli/config/variables-ci --development
    export PATH=$PATH:$SICES_PATH/devops/cli
    ```
    > **Nota**: lembre-se de substitir `[PROJECT_PATH]` pelo caminho do projeto.

    - ##### 3.2.3. *Carregue as alterações do arquivo bash*
    ```
    $ source ~/.bashrc
    ```

    - ##### 3.2.4. *Na variável `PATH` agora devem aparecer alguns caminhos relacionados a pasta do projeto*
    ```
    $ echo $PATH | grep sices
    ```
    > **Nota**: caso o comando acima não possua retorno revise os passos de instalação e reinicie o sistema operacional.

  <a name="aplicacao--ambiente"></a><a name="3.3"></a>
  - [3.3](#aplicacao--ambiente) **Dependências de ambiente**:

    - ##### Para visualizar as dependências a serem instaladas execute:
    ```
    $ devops/cli/ces-ambience-install --installation-list
    ```

    - ##### Você pode instala-las manualmente (recomendado), ou executar o comando abaixo para instalação automatica:
    ```
    $ devops/cli/ces-ambience-install --full
    ```

  <a name="aplicacao--sices"></a><a name="3.4"></a>
  - [3.4](#aplicacao--sices) **Configuração da plataforma**:

    - ##### 3.4.1. *Instalando as dependências do Sices*
    ```
    $ ces-app-install
    ```

    - ##### 3.4.2. *Configurando a aplicação*
    ```
    $ ces-app-config --development
    ```

    - ##### 3.4.3. *Compilando o frontend*
    ```
    $ ces-frontend-compile
    ```

    - ##### 3.4.4. *Iniciando a aplicação*
    ```
    $ ces-app-start
    ```
    > Após os passos acima a aplicação estará disponível em: `http://localhost:8000`


**[⬆ Voltar ao Topo](#sumário)**

## Padrões de Desenvolvimento

  <a name="padroes-desenvolvimento--variaveis-ambiente"></a><a name="4.1"></a>
  - [4.1](#padroes-desenvolvimento--variaveis-ambiente) **Definição de Variáveis de Ambiente**:

    > devops/cli/config/

    #### Regras Gerais:

    - Variáveis usadas por vários ambientes devem ser definidas no arquivo `variables` usando como padrão o nome da aplicação, variante, e nome da variável.
      ```
      Ex.:

      SICES_MAILER_HOST

      Aplicação: Sices
      Variantes: Mailer
      Variável: Host
      ```

    - Variáveis usadas exclusivamente em um ambiente devem ser definidas no arquivo `variables` dentro da pasta de seu respectivo ambiente, usando como padrão o nome da aplicação, ambiente, variante, e nome da variável.
      ```
      Ex.:

      SICES_HOMOLOG_DATABASE_HOST

      Aplicação: Sices
      Ambiente: Homolog
      Variantes: Database
      Variável: Host
      ```

    - Variáveis definidas exclusivamente para um ambiente devem ser exportadas no arquivo variables-ci com nome genérico, de maneira a tornar-sem dinâmicas conforme mudança de ambiente. Utilizamos como padrão o prefixo CES no início, o nome da aplicação, variante, e nome da variável.
      ```
      Ex.:

      CES_SICES_DATABASE_HOST

      Aplicação: Sices
      Variantes: Database
      Variável: Host
      ```

    > **Nota**: As variáveis usadas pelo Symfony são inseridas automaticamente no arquivo `parameters.yml` ao rodar o comando ces-app-config. Variáveis utilizadas pelo Symfony devem ter seu sample definido nos arquivos `backend/app/config/parameters_sample.yml` e `devops/cli/util/variables-replace`.


**[⬆ Voltar ao Topo](#sumário)**

## Execução de Tarefas

  <a name="execução-tarefas--comandos"></a><a name="5.1"></a>
  - [5.1](#execução-tarefas--comandos) **Comandos de execução**: Comandos para execução de tarefas em ambiente local.

    Preparar ambiente para a execução da tarefa:
    ```
    $ ces-new-task issue-[ISSUE_NUMBER]
    ```

    Adicionar arquivos modificados, realizar commit e envio ao repositório:
    ```
    $ git add [FILE_OR_PATH]
    $ git commit -m "[WHAT_WERE_MADE]"
    $ git push origin issue-[ISSUE_NUMBER]
    ```

    Deletar branch local após merge de Pull Request:
    ```
    $ git branch -D issue-[ISSUE_NUMBER]
    ```


**[⬆ Voltar ao Topo](#sumário)**

## Comandos

  <a name="comandos--ambientes"></a><a name="6.1"></a>
  - [6.1](#comandos--ambientes) **Configuração**:

    - ##### Instalação de dependências de ambientes
      ```
      $ ces-ambience-install --full
      ```

    - ##### Listagem de dependências de ambientes
      ```
      $ ces-ambience-install --installation-list
      ```

    - ##### Instalação de dependências das aplicações
      ```
      $ ces-app-install
      ```

    - ##### Configuração de aplicações
      (default: development)
      ```
      $ ces-app-config --[AMBIENCE]
      ```

  <a name="comandos--acesso"></a><a name="6.2"></a>
  - [6.2](#comandos--acesso) **Acesso**:

    - ##### Acesso ao ambiente de homolog
      ```
      $ ssh -i $SICES_PATH/devops/aws/homolog.pem admin@54.233.150.10
      ```

    - ##### Acesso ao ambiente de staging
      ```
      $ ssh -i $SICES_PATH/devops/aws/staging.pem admin@18.231.15.228
      ```

  <a name="comandos--operacionais"></a><a name="6.3"></a>
  - [6.3](#comandos--operacionais) **Deploy e Start**:

    - ##### Deploy
      (default ambience: no default value, default origin: staging, obs: origin only for production)
      ```
      $ cd AMBIENCE
      $ ces-app-deploy --[AMBIENCE] --[ORIGIN]
      ```

    - ##### Start de aplicações
      (default ambience: development, default application: only sices)
      ```
      $ ces-app-start --[AMBIENCE] --[APPICATION]
      ```

  <a name="comandos--backend"></a><a name="6.4"></a>
  - [6.4](#comandos--backend) **Backend**:

    - ##### Pequisa de rotas
      (default: no arg, show all)
      ```
      $ ces-route-list --'[STRING]'
      ```

    - ##### Requisição de token
      (no default value)
      ```
      $ ces-token-request --[APPLICATION] --[AMBIENCE]
      ```

  <a name="comandos--banco"></a><a name="6.5"></a>
  - [6.5](#comandos--banco) **Banco de dados**:

    - ##### Atualizar o banco de dados local com a versão de homolog
      ```
      $ ces-database-mirror
      ```

    - ##### Atualizar schema do banco de dados
      ```
      $ ces-database-update
      ```

  <a name="comandos--processos"></a><a name="6.6"></a>
  - [6.6](#comandos--processos) **Processos**:

    - ##### Limpeza de arquivos de logs da aplicação
      ```
      $ ces-log-clear
      ```

    - ##### Limpeza de cache do Symfony
      ```
      $ ces-cache-clear
      ```

    - ##### Compilar frontend
      ```
      $ ces-frontend-compile
      ```

  <a name="comandos--outros"></a><a name="6.7"></a>
  - [6.7](#comandos--outros) **Outros**:

    - ##### Notificar chat #sices-devops
      ```
      $ ces-slack-notify '[MESSAGE]'
      ```

    - ##### Corrigir permissões de arquivos e pastas
      (default: development)
      ```
      $ ces-permission-fix --[AMBIENCE]
      ```

    - ##### Para lint de aplicações
      ```
      $ cd [APP_PATH]
      $ yarn lint
      ```


**[⬆ Voltar ao Topo](#sumário)**

## Sobre

  <a name="sobre--equipe"></a><a name="7.1"></a>
  - [7.1](#sobre--equipe) **A equipe**:

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
    Product manager
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

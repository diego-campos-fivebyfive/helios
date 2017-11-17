SICES SOLAR
===========

Sistema de suporte para empresas do setor de energia solar fotovoltaíca.

##### [https://app.plataformasicessolar.com.br](https://app.plataformasicessolar.com.br)

## Sumário

  1. [Guia Geral](#guia-geral)
  1. [Workflow](#workflow)
  1. [Instalações da Aplicação](#instalações-da-aplicação)
  1. [Padrões de Desenvolvimento](#padrões-de-desenvolvimento)
  1. [Execução e Gerenciamento de Tarefas](#execução-e-gerenciamento-de-tarefas)
  1. [Lista de Comandos](#comandos)
  1. [Gerenciamento de Arquivos](#gerenciamento-de-arquivos)
  1. [Status](#status)
  1. [Estrutura](#estrutura)
  1. [Links Uteis](#links-uteis)
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
    - AWS: Amazon Web Services
    - S3: Amazon Simple Storage Service

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

  <a name="workflow--fluxo"></a><a name="2.2"></a>
  - [2.2](#workflow--fluxo) **Levantamento e distribuição de tarefas**:

    - 2.2.1. Draft (Github):

      Consiste no levantamento de demanda semanal em reunião de equipe técnica com equipe de produto, onde são debatidas e anotadas todas as solicitações para serem convertidas em roadmap posteriormente.

    - 2.2.2. Roadmap (Trello):

      Consiste na distribuição das solicitações levantadas no `Draft` em cards e checklists, sendo esses, subdivisões das solicitações por área, que posteriormente servem de base para a criação de tarefas técnicas e acompanhamento de progresso em um âmbito geral. Em resumo cards são agrupamentos por área e checklists dizem a respeito do processo, ou "o que é a funcionalidade" ou "o que ela deve fazer".

    - 2.2.3. Tasks (Waffle):

     São as menores fragmentações do processo, são as tarefas técnicas executadas para que uma determinada funcionalidade seja implementada, sendo que essas nem sempre são independentes, e são necessárias diversas tarefas técnicas para completar um item de checklist do roadmap.

  <a name="workflow--tarefas"></a><a name="2.3"></a>
  - [2.3](#workflow--tarefas) **Ciclo de vida de Tarefas**:

    - Para cada tarefa há um prazo máximo de execução de 2 dias;
    - Caso a execução de uma tarefa ultrapasse 2 dias a mesma deve ser reavaliada pela a equipe;
    - Tarefas devem ser quebradas em caso de:
      - Tarefas muito grandes;
      - Tarefas que modifiquem diversas áreas distintas do projeto;
      - Tarefas em que a execução ultrapasse os 2 dias.

      ##### Execução de tarefas em fluxo normal:

        ```
        1. Iniciada em Backlogs (general, devops)
        2. Incluida em Sprint Semanal (to do)
        3. Executada pelo Desenvolvedor (in progress)
        4. Enviada para Revisão de código pela equipe (review)
        5. Disponibilizada para teste em Homolog pelo desenvolvedor (testing I)
        6. Revisada pela equipe de produto, caso necessário (testing II)
        7. Marcada como concluída (done)
        ```

      ##### Execução de tarefas por fluxo reiniciado:

        Após o merge da pull request atual, a tarefa pode ser reiniciada para continuidade do trabalho, caso necessário, seguindo o seguinte fluxo:

        ```
        1. Disponibilizada para teste em Homolog pelo desenvolvedor (testing I)
        2. Reiniciada pelo Desenvolvedor para continuidade de execução em mesma issue (in progress)
        ```

      ##### Execução de tarefas por fluxo quebrado:

        Após iniciada uma tarefa, se houver necessidade de iniciar uma tarefa de maior importância de imediato, ou caso a execução da tarefa seja impedida por algum motivo, a mesma pode ser bloqueada, seguindo o seguinte fluxo:

        ```
        1. Sendo executada pelo Desenvolvedor (in progress)
        2. Apontada como execução impossibilitada ou pausada (blocked)
        ```

        Caso a branch da tarefa ainda não contenha ajustes existe também a opção de cancelar o trabalho na issue, seguindo o seguinte fluxo:

        ```
        1. Sendo executada pelo Desenvolvedor (in progress)
        2. Cancelada execução pelo Desenvolvedor por não conter modificações (to do)
        ```

  <a name="workflow--review"></a><a name="2.4"></a>
  - [2.4](#workflow--review) **Revisão de Pull Request**:

    - As revisões de Pull Request devem ser feitas exclusivamente através do Github;
    - Comentários devem ser feitos na Pull Request e avisados via Slack;
    - É proibido realizar merge de Pull Request sem responder aos comentários;

  <a name="workflow--flags"></a><a name="2.5"></a>
  - [2.5](#workflow--flags) **Solicitações no Slack**: utilizamos por padrão flags de classificações no início de cada solicitação.

    - **HELP**: para solicitar ajuda/pair (chat [tech](https://kolinalabs-si.slack.com/messages/C65HXPEQM))
    - **REVIEW**: para solicitar review (chat [devops](https://kolinalabs-si.slack.com/messages/C64ACCF2M))
    - **TEST**: para solicitar test (chat [devops](https://kolinalabs-si.slack.com/messages/C64ACCF2M))

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

      → [Tutorial Github](https://help.github.com/articles/adding-a-new-ssh-key-to-your-github-account)

    - ##### 3.1.5. *Clonando o repositório do Github*
    ```
    $ git clone git@github.com:sices/sices.git
    ```

  <a name="aplicacao--bash"></a><a name="3.2"></a>
  - [3.2](#aplicacao--bash) **Bash Profile**:

    - ##### 3.2.1. *Abra o Arquivo `.profile` com seu editor (Vim, Nano ou outro)*
    ```
    $ sudo vim ~/.profile
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
    $ source ~/.profile
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

  <a name="aplicacao--outros"></a><a name="3.5"></a>
  - [3.5](#aplicacao--outros) **Configurações adicionais (opcional)**:

    - ##### 3.5.1. *Alias remote push*
      ```
      remote_push() {
        if [[ $1 = '--pr' ]]; then
          ces-issue-request --review
        else
          git push origin $(git symbolic-ref HEAD | sed -e 's,.*/\(.*\),\1,')
        fi
      }

      alias remote-push="remote_push"
      ```

      Uso:
      - `remote-push` é similar ao `git push origin [BRANCH_NAME]`
      - `remote-push --pr` é similar a `ces-issue-request --review`

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

## Execução e gerenciamento de Tarefas

  <a name="tarefas--automatizacao"></a><a name="5.1"></a>
  - [5.1](#tarefas--automatizacao) **Movimentação automatica de issues por ação**:

    - ##### 5.1.1. **Fluxo Normal**
      ```
      In progress: ces-issue-start
      Review: ces-issue-request --review
      Testing I: pull request merge
      Testing II: ces-issue-start (from Testing I | accept [y])
      Done: ces-issue-start (form Testing I | decline [n])
      ```

    - ##### 5.1.2. **Fluxo Reiniciado**
      ```
      In progress: ces-issue-restart
      ```

    - ##### 5.1.3. **Fluxo Quebrado**
      ```
      To do: ces-issue-start (from In progress | decline [n])
      Blocked: ces-issue-start (from In progress | accept [y])
      In progress: ces-issue-start
      ```

  <a name="tarefas--start"></a><a name="5.2"></a>
  - [5.2](#tarefas--start) **Processos executados pelo comando ces-issue-start**:

    | Stage From  | BG\|BD\|TD | B                | IP     | IP   | R   | TI     | TI     | TII\|D |
    | :---        | :---:      | :---:            | :---:  | :--: | :-: | :---:  | :---:  | :---:  |
    | Conditional | --         | --               | [N]    | [Y]  | --  | [Y]    | [N]    | --     |
    | Branch      | Create     | Create \| Update | Remove | Keep | X   | Delete | Delete | --     |
    | Assign      | Assign     | Assign           | Remove | Keep | X   | Keep   | Keep   | --     |
    | Stage To    | IP         | IP               | TD     | B    | X   | TII    | D      | D\*    |

    > \* Movimentação Manual

    - #### Legenda:
    ```
    BG: Backlog General       IP: In progress
    BD: Backlog Devops        B: Blocked
    TD: To do                 R: Review
    TI: Testing I             D: Done
    TII: Testing II

    [N]: Decline          --: Not exist
    [Y]: Accept           X: Not permited
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
      ###### default: development
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
      ###### default ambience: no default value | default origin: staging
      ```
      $ cd AMBIENCE
      $ ces-app-deploy --[AMBIENCE] --[ORIGIN]
      ```
      > **Nota**: Argumento ORIGIN apenas disponivel para AMBIENCE production

    - ##### Start de aplicações
      ###### default ambience: development | default application: only sices
      ```
      $ ces-app-start --[AMBIENCE] --[APPICATION]
      ```

  <a name="comandos--backend"></a><a name="6.4"></a>
  - [6.4](#comandos--backend) **Backend**:

    - ##### Pequisa de rotas
      ###### default: no arg, show all
      ```
      $ ces-route-list '[STRING]'
      ```

    - ##### Requisição de token
      ###### default: no default value
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

  <a name="comandos--tarefas"></a><a name="6.7"></a>
  - [6.7](#comandos--tarefas) **Comandos de gerenciamento de tarefas**:

    Preparar ambiente para a execução da tarefa:
    ```
    $ ces-issue-start [ISSUE_NUMBER]
    ```

    Mover tarefa no quadro:
    ```
    $ ces-issue-move [ISSUE_NUMBER] --[STAGE_TO]
    ```

    Assinar tarefa no quadro:
    ```
    $ ces-issue-assign [ISSUE_NUMBER] [GITHUB_USER_NAME]
    ```

    Abrir review de tarefa:
    ```
    $ ces-issue-request --review
    ```

    Requisitar teste de tarefa:
    ```
    $ ces-issue-request --testing
    ```

    Informações sobre uma tarefa:
    ```
    $ ces-issue-info [ISSUE_NUMBER] --[INFO_TYPE]
    ```

    Finalizar tarefa:
    ```
    $ ces-issue-close [ISSUE_NUMBER]
    ```

    Bloquear tarefa:
    ```
    $ ces-issue-block [ISSUE_NUMBER]
    ```

    Reiniciar trabalho em tarefa:
    ```
    $ ces-issue-restart
    ```

  <a name="comandos--outros"></a><a name="6.8"></a>
  - [6.8](#comandos--outros) **Outros**:

    - ##### Notificar chat #sices-devops
      ```
      $ ces-slack-notify --[CHANNEL] '[MESSAGE]'
      ```

    - ##### Corrigir permissões de arquivos e pastas
      ###### default: development
      ```
      $ ces-permission-fix --[AMBIENCE]
      ```

    - ##### Para lint de aplicações
      ```
      $ cd [APP_PATH]
      $ yarn lint
      ```

    - ##### Adicionar arquivos modificados, realizar commit e envio ao repositório
      ```
      $ git add [FILE_OR_PATH]
      $ git commit -m "[WHAT_WERE_MADE]"
      $ git push origin issue-[ISSUE_NUMBER]
      ```


**[⬆ Voltar ao Topo](#sumário)**

## Gerenciamento de Arquivos

  <a name="uploads--buckets"></a><a name="7.1"></a>
  - [7.1](#uploads--buckets) **Amazon S3**:

    - 7.1.1. **Carteiras**:
      ```
      pss-general
      pss-homolog-private
      pss-homolog-public
      pss-production-private
      pss-production-public
      ```

    - 7.1.2. **Nomenclaturas**: Toda carteira segue o padrão de nomenclatura inciado por `pss`, seguido por `ambiente` e `access`, que é o tipo de acesso.

    - 7.1.3. **Tipos de acesso**: São distribuidos entre `private` e `public`, onde, os arquivos de tipo privado, apenas podem ser acessados via pianel adm. do S3 ou através de download pelos sistema Sices; os arquivos públicos por sua vez estão disponíveis diretamente para acesso através dos links do S3.

  <a name="uploads--local"></a><a name="7.2"></a>
  - [7.2](#uploads--local) **Arquivos temporários**:

    - 7.2.1. **Pasta .uploads**: Nessa pasta encontram-se documentos de usuários do sistema, como por exemplo proformas e propostas geradas.

    - 7.2.2. **Processo**: Durante o processo de upload ou geração de um arquivo o mesmo será salvo no sistema dentro da sua respectiva pasta temporária, sendo enviado na sequência para o S3 e "excluido" da pasta temporária a cada deploy realizado.

    - 7.2.3. **Pastas de agrupamento**: Cada pasta temporária segue o padrão de estrutura da seguite forma: `root` que corresponde ao módulo do documento e `type` que correponde ao tipo de documento.
      ```
      Ex:
      $SICES_PATH/.uploads/order/proforma/

      Root: Order
      Type: Proforma
      ```

  <a name="uploads--file"></a><a name="7.3"></a>
  - [7.3](#uploads--file) **Nomenclatura de arquivos**: Como o S3 e a pasta de arquivos temporários seguem o padrão de `root`/`type` os arquivos devem ser salvos não utilizando sufixos que contenham essas informações, devem conter identificadores unicos gerados randomicamente junto com suas extensões.
    ```
    Ex. Proposal: 1a86be152bb4663e118d57a428ee70fd.pdf
    ```

  <a name="uploads--services"></a><a name="7.4"></a>
  - [7.4](#uploads--services) **Services**: Para gerenciamento dos arquivos o sistema disponibiliza dois serviços, sendo eles:

    - 7.4.1. **Storage** (app_storage): Serviço de gerenciamento de alocação de arquivos e leitura, disponibiliza funções de upload, integração com o S3 e exibição de arquivos.

    - 7.4.2. **Generator** (app_generator): Serviço de criação de arquivos, como por exemplo o [Gerador de PDF](#gerador-de-pdf).

  <a name="uploads--teste-local"></a><a name="7.5"></a>
  - [7.5](#uploads--teste-local) **Teste Local de Geração, Arquivamento (temp e S3) e Exibição de arquivos**:

    - 7.5.1. **Execução até falha**: gere o PDF e aguarde aproximadamente um minuto até que uma mensagem de falha apareça na tela, em seguida o PDF estará disponível na pasta `$SICES_PATH/.uploads`.

      > Essas parte se faz necessária pois localmente o PDF apenas é gerado após timeout no processo de geração (aprox. 1 minuto após execução).

    - 7.5.2. **Atribuição de File Name estático**: cria uma nova váriavel `$filename` na controller que chama o gerador, onde a mesma recebe nome do PDF gerado. Em seguida comente a linha original. Exemplo:
      ```php
      //$filename = md5(uniqid(time())) . '.pdf';
      $filename = 'f6971985f1787ec54a4fef316d8064ab.pdf';
      ```

    - 7.5.3. **Ignorando a criação de novo PDF**: para que o processo possa ser executado até o final comente a linha que solicita a geração de um novo PDF.
      ```php
      //$this->get('app_generator')->pdf($options, $file);
      ```

     > Com as etapas acima implementadas será possivel testar o fluxo completo usando PDF estático.


**[⬆ Voltar ao Topo](#sumário)**

## Status

  <a name="status--conta"></a><a name="8.1"></a>
  - [8.1](#status--conta) **Conta**:

    | Constante | Valor | Descrição |
    | :--- | :---: | :---|
    | PENDING | 0 | Conta cadastrada, link de verificação de email enviado. |
    | STANDING | 1 | Conta verificada, aguardando aprovação por usuário sices. |
    | APPROVED | 2 | Conta aprovada, link de confirmação (configuração de senha) enviado. |
    | ACTIVATED | 3 | Conta ativada, acesso liberado. |
    | LOCKED | 4 | Conta bloqueada. |

  <a name="status--orçamento"></a><a name="8.2"></a>
  - [8.2](#status--orçamento) **Orçamento**:

    | Constante | Valor | Descrição |
    | :--- | :---: | :---|
    | STATUS_BUILDING | 0 | Orçamento em edição. |
    | STATUS_PENDING | 1 | Orçamento enviado, aguardando validação de sices comercial. |
    | STATUS_VALIDATED | 2 | Orçamento validado, aguardando aprovação de integrador. |
    | STATUS_APPROVED | 3 | Orçamento aprovado, aguardando pagamento. |
    | STATUS_REJECTED | 4 | Orçamento rejeitado, ações bloqueadas. |
    | STATUS_DONE | 5 | Orçamento concluído, pagamento efetuado. |
    | STATUS_INSERTED | 6 | Orçamento lançado (em CRM Protheus) para produção. |
    | STATUS_AVAILABLE | 7 | Produto (Sistema) disponível para coleta. |
    | STATUS_COLLECTED | 8 | Produto (Sistema) coletado para entrega. |
    | STATUS_BILLED | 9 | Produto (Sistema) faturado. |
    | STATUS_DELIVERED | 10 | Produto (Sistema) entregue. |

  <a name="status--orçamento-origem"></a><a name="8.3"></a>
  - [8.3](#status--orçamento-origem) **Origem de Orçamentos**:

    | Constante | Valor | Descrição |
    | :--- | :---: | :---|
    | SOURCE_ACCOUNT | 0 | Quando iniciado por um integrador |
    | SOURCE_PLATFORM | 1 | Quando iniciado por um usuário sices |


**[⬆ Voltar ao Topo](#sumário)**

## Estrutura

  <a name="estrutura--ambience"></a><a name="9.1"></a>
  - [9.1](#estrutura--ambience) **Ambientes**:

    - `local`: servidor local de desenvolvimento, configurado na máquina de cada desenvolvedor
    - `homolog`: servidor remoto de teste, configurado para receber deploy automaticamente
    - `production`: servidor remoto de produção, base final de uso de deploy manual

  <a name="estrutura--raiz"></a><a name="9.2"></a>
  - [9.2](#estrutura--raiz) **Pastas raiz**:

    - `backend`: pasta main do projeto, contendo arquivos do sistema
    - `devops`: pasta de uso geral de devops, como operações de ecossistema, processos, etc
    - `docs`: além do `README.md`, utilizamos essa pasta para documentações de arquivos e UML

  <a name="estrutura--temp"></a><a name="9.3"></a>
  - [9.3](#estrutura--temp) **Pastas de arquivos temporários**:

    - `.temp`: pasta para arquivos temporários de deploy, usada exclusivamente em produção
    - `.backup`: pasta para backup de arquivos antes de deploy, usada exclusivamente em produção
    - `.mirror`: pasta para export de arquivos de mirror de SQL
    - `.uploads`: pasta arquivamento temporário de arquivos gerados e de downlaod do S3


**[⬆ Voltar ao Topo](#sumário)**

## Links Uteis

  <a name="links--doc"></a><a name="10.1"></a>
  - [10.1](#links--doc) **Links de documentação**:

    - [Integração - Protheus](docs/integrations/protheus)
    - [Roadmap - Draft Semanal](docs/meetings/roadmap/draft.md)
    - [Diagramas - UML](docs/uml/diagrams.md)
    - [Tasks - Millestons](https://github.com/sices/sices/milestones)


**[⬆ Voltar ao Topo](#sumário)**

## Sobre

  <a name="sobre--equipe"></a><a name="11.1"></a>
  - [11.1](#sobre--equipe) **A equipe**:

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
    Sysadmin
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

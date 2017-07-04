Ambiente de Desenvolvimento
===========================

A distro Ubuntu 16.04 foi utilizada como base de referência para
a elaboração deste guia e o processo de instalação abaixo pode
estar sujeito a pequenas mudanças em outras distribuições.


1. Clonando o projeto do Github
-------------------------------

#### HTTP:
```
$ git clone https://[USER_NAME]@bitbucket.org/kolinalabsdevelopers/sices.git
```
#### SSH:
```
$ git clone git@bitbucket.org:kolinalabsdevelopers/sices.git
```


2. Instalando o Composer
------------------------

```
$ sudo apt install composer
```


3. Instalando dependências
--------------------------

```
$ cd [PROJECT_FOLDER]
$ composer install
```


4. Configurações do App
-----------------------

#### Criando o arquivo de configurações:

Entre na pasta de configurações do app em `app/config` e
duplique o arquivo `config-sample.yml` com o nome `config.yml`

```
$ cd app/config
$ cp config-sample.yml config.yml
```

#### Atribuindo o caminho padrão do Node:

Na sessão `Assetic configuration` atribua a váriavel
`node` o caminho do node em seu equipamento.


5. Instalando o Assetic
-----------------------

```
$ npm install
```
ou
```
$ yarn install
```


6. Compilando o front-end
-------------------------

```
$ npm run compile
```
ou
```
$ yarn compile
```


7. Rodando a Plataforma
-----------------------

```
$ npm start
```
ou
```
$ yarn start
```


Após os passos acima a aplicação estará disponível em: `http://localhost:8000`


Proxima leitura: [Sobre o Workflow da Equipe](workflow.md)

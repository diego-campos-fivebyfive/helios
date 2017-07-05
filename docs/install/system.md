Instalação do Sistema
=====================

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


2. Adicionando o `PATH` do Projeto
----------------------------------

Abra o Arquivo `.bashrc` com seu editor (Vim, Nano ou outro):
```
$ sudo vim ~/.bashrc
```

Adicione ao final do arquivo as linhas:
```
export SICES_PATH=/home/knd/sices
export PATH=$PATH:$SICES_PATH/cli
```


3. Instalando dependências do Sistema
-------------------------------------

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
$ yarn install
```


6. Compilando o front-end
-------------------------

```
$ yarn compile
```


7. Rodando a Plataforma
-----------------------

```
$ yarn start
```


Após os passos acima a aplicação estará disponível em: `http://localhost:8000`


[Voltar](../getting-started.md)

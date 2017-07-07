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

Copie o caminho do seu projeto:
```
$ cd [PROJECT_FOLDER]
$ pwd
```

Adicione ao final do arquivo as linhas substituindo PROJECT_PATH pelo caminho do projeto do passo anterior:
```
export SICES_PATH=PROJECT_PATH
export PATH=$PATH:$SICES_PATH/cli
```

Carregue as alterações do arquivo bash:
```
$ source ~/.bashrc
```

Na variavel PATH agora devem estar disponiveis: o caminho para o projeto e o caminho da pasta cli:
```
$ echo $PATH
```

Caso os caminhos não apareçam, reinicie a maquina.


3. Instalando e configurando dependências do Sistema
----------------------------------------------------

```
$ yarn install && composer install && ces-server-config
```


4. Compilando o Frontend
------------------------

```
$ yarn compile
```


5. Rodando o Sistema
--------------------

```
$ yarn start
```


Após os passos acima a aplicação estará disponível em: `http://localhost:8000`


## Resolução de Problemas:

#### Erro devido sistema não ter encontrado o node

1. Verifique a instalação do node
--------------------------------

2. Verifique a variavel node
----------------------------

Na sessão `Assetic configuration` do documento `app/config/config.yml` verifique se o campo
`node` está com a mesma váriavel usada em seu sistema operacional para rodar o node
(ex: node, nodejs, /urs/bin/node ...)


[Voltar](../getting-started.md)

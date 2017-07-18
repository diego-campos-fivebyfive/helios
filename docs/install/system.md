Instalação do Sistema
=====================

1. Adicionando o PATH do Projeto
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

Adicione ao final do arquivo as linhas substituindo `PROJECT_PATH` pelo caminho do projeto do passo anterior:
```
export SICES_PATH=PROJECT_PATH
source $SICES_PATH/cli/ces-variables
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

Caso os caminhos não apareçam, verifique se o mesmo está correto e reinicie a maquina.


2. Instalando e configurando dependências do Sistema
----------------------------------------------------

```
$ yarn install && composer install && ces-server-config --development
```


3. Compilando o Frontend
------------------------

```
$ yarn compile
```


4. Rodando o Sistema
--------------------

```
$ yarn start:dev
```

Após os passos acima a aplicação estará disponível em: `http://localhost:8000`


5. Adicionando Virtual Host
---------------------------

Abra o arquivo `/etc/hosts` com seu editor (Vim, Nano ...):
```
$ sudo vim /etc/hosts
```

Adicione na lista de hosts a linha abaixo:
```
127.0.0.1     sicessolar.dev
```

Com isso o sistema estará disponível na url: `sicessolar.dev:8000`


Caso haja algum problema com a instalação consulte a
[Sessão de resolução de problemas](system-install-problems.md).


[Voltar](../getting-started.md)

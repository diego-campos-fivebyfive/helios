Instalação do Sistema
=====================

1. Clonando o projeto do Github
-------------------------------

#### HTTP:
```
$ git clone https://[USER_NAME]@bitbucket.org/cjchamado/sices.git
```
#### SSH:
```
$ git clone git@bitbucket.org:cjchamado/sices.git
```


2. Adicionando o PATH do Projeto
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


6. Adicionando Virtual Host (Opcional)
--------------------------------------

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
[Sessão de resolução de problemas](system-install-problems.md)


[Voltar](../getting-started.md)

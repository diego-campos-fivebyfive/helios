Instalação de Dependências
==========================

1. Instalando o Apache
----------------------

```
$ sudo apt-get update
$ sudo apt-get install apache2
```


2. Instalando o PHP
----------------------

```
$ sudo apt-get install php
```

Edite o arquivo `/etc/apache2/mods-enabled/dir.conf` com o seguinte conteúdo:
```
<IfModule mod_dir.c>
  DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```

Instale os pacotes `php-mbstring`, `php-xml`, `php-mysql` e `php-intl` usando o comando:
```
$ sudo apt install php-mbstring php-xml php-mysql php-intl
```


3. Instalando o Nodejs
----------------------

```
$ sudo apt-get install nodejs
```


4. Instalando o Yarn
--------------------

```
$ curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
$ echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
$ sudo apt-get update && sudo apt-get install yarn
```


5. Instalando o Composer
------------------------

```
$ sudo apt install composer
```


6. Instalando o Git
-------------------

```
$ sudo apt install git
```

Configurando o git:
```
$ git config --global user.email "mail@mail.com"
$ git config --global user.name "Full Name"
```


[Proximo passo: instalação do sistema](system.md)

[Voltar](../getting-started.md)

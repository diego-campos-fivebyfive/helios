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


3. Configurando o PHP

Edite o arquivo `/etc/apache2/mods-enabled/dir.conf` com o seguinte conteúdo:

```
<IfModule mod_dir.c>
  DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```


4. Instalando os pacotes do PHP

Instale os pacotes `php-mbstring`, `php-xml` e `php-intl` usando o comando:

```
$ sudo apt install [PACOTE_NAME]
```


5. Instalando o Nodejs
----------------------

```
$ sudo apt-get install nodejs
```


6. Instalando o Yarn
--------------------

```
$ sudo apt-get install yarn
```


7. Instalando o Composer
------------------------

```
$ sudo apt install composer
```


8. Instalando o Git
-------------------

```
$ sudo apt install git
```


9. Configurando o Git
---------------------

```
$ git config --global user.email "mail@mail.com"
$ git config --global user.name "Full Name"
```

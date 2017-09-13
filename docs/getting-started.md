Ambiente de Desenvolvimento
===========================

A distro Ubuntu 16.04 foi utilizada como base de referência para
a elaboração deste guia e o processo de instalação abaixo pode
estar sujeito a pequenas mudanças em outras distribuições.


1. Instalando e Configurando o Git
----------------------------------

### Instalando o Git
```
$ sudo apt install git
```

### Configurando a SSH
```
$ ssh-keygen -t rsa -b 4096 -C "mail@mail.com"
$ cat ~/.ssh/id_rsa.pub
```

### Configurando Globais
```
$ git config --global user.email "mail@mail.com"
$ git config --global user.name "Full Name"
```


2. A - Clonando e configurando o repositório SICES
--------------------------------------------------

```
$ git clone git@github.com:sices/sices.git
$ git remote add upstream git@github.com:sices/tech.git
```

2. B - Clonando e configurando repositório TECH
-----------------------------------------------

```
$ git clone git@github.com:sices/tech.git
$ git remote add upstream git@github.com:sices/sices.git
```


3. Instalação de Dependências
-----------------------------

Caso você já possua os requisitos abaixo intalados, pule para a proxima etapa:

#### Requisitos

  - Apache2
  - PHP
    * php-mbstring
    * php-xml
    * php-mysql
    * php-intl
  - Node
  - Yarn
  - Composer
  - Git


#### Instalação

Você pode instalar as dependências utilizando o comando:
```
$ ./cli/ces-install-dependencies
```

ou

Instala-las individualmente:

  - [Instalando dependências individualmente](install/dependencies-list.md)


4. Configuração do Apache (Opicional)
-------------------------------------

Edite o arquivo `/etc/apache2/mods-enabled/dir.conf` com o seguinte conteúdo:

```
<IfModule mod_dir.c>
  DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```


5. Instalação do Sistema
------------------------

Para instalação do sistema siga os passos do documento:

  - [Instalação do sistema](install/system.md)


[Voltar](../README.md)

Ambiente de Desenvolvimento
===========================

A distro Ubuntu 16.04 foi utilizada como base de referência para
a elaboração deste guia e o processo de instalação abaixo pode
estar sujeito a pequenas mudanças em outras distribuições.

1. Git
------

### Instalando o Git
```
$ sudo apt install git
```

### Configurando a SSH
```
$ssh-keygen -t rsa -b 4096 -C "mail@mail.com"
$cat ~/.ssh/id_rsa.pub
```

### Configurando Globais
```
$ git config --global user.email "mail@mail.com"
$ git config --global user.name "Full Name"
```

### Clonando o repositório
```
$ git clone git@bitbucket.org:cjchamado/sices.git
```



1. Instalação de Dependências
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


Para instalação das dependências siga os passos do documento:

  - [Instalação de dependências](install/dependencies.md)


2. Instalação do Sistema
------------------------

Para instalação do sistema siga os passos do documento:

  - [Instalação do sistema](install/system.md)


[Voltar](../README.md)

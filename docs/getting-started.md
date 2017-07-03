Ambiente de Desenvolvimento
===========================

A distro Ubuntu 16.04 foi utilizada como base de referência para
a elaboração deste guia e o processo de instalação abaixo pode
estar sujeito a pequenas mudanças em outras distribuições.


1. Clonando o projeto do Github
-------------------------------

#### HTTP:
```
$ git clone https://rafamikovski@bitbucket.org/kolinalabsteam/sices-solar.git
```
#### SSH:
```
$ git clone git@bitbucket.org:kolinalabsteam/sices-solar.git
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


4. Rodando a Plataforma
-----------------------

```
$ cd [PROJECT_FOLDER]
$ php app/console server:run
```

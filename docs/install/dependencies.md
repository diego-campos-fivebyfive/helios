Instalação de Dependências
==========================

1. Atualizando o Sistema
------------------------

```
$ sudo apt-get update
```


2. Instalação das dependências
------------------------------

Você pode instalar as dependências do sistema individualmente ou utilizando o comando `ces-install-dependencies`:

#### Instalando todas com o comando ces-install-dependencies:

```
$ ces-install-dependencies
```

ou

#### [Instalando dependências "uma a uma"](dependencies-list.md)


3. Configurando o PHP (Opcional)
--------------------------------

Edite o arquivo `/etc/apache2/mods-enabled/dir.conf` com o seguinte conteúdo:

```
<IfModule mod_dir.c>
  DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```


4. Configurando o Git (Opcional)
--------------------------------

```
$ git config --global user.email "mail@mail.com"
$ git config --global user.name "Full Name"
```


[Proximo passo: instalação do sistema](system.md)

[Voltar](../getting-started.md)

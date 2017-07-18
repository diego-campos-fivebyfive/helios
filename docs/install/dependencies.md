Instalação de Dependências
==========================

1. Atualizando o Sistema
------------------------

```
$ sudo apt-get update
```


2. Instalação das dependências
------------------------------

Você pode instalar as dependências do sistema individualmente ou utilizando o comando?
```
$ ./cli/ces-install-dependencies
```

ou

Instala-las individualmente:
  - [Instalando dependências individualmente](dependencies-list.md)


3. Configurando o PHP
---------------------

Edite o arquivo `/etc/apache2/mods-enabled/dir.conf` com o seguinte conteúdo:

```
<IfModule mod_dir.c>
  DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```


[Proximo passo: instalação do sistema](system.md)

[Voltar](../getting-started.md)

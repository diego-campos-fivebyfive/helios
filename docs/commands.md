Lista de Comandos
=================

Comandos utilizados no sistema Sices Solar.

### Para iniciar uma nova branch

```
$ ces-new-task issue-NUMBER
```

### Para enviar notificação para o #sices-devops no slack

```
$ ces-slack-notify 'MESSAGE'
```


### Para atualizar a banco de dados local com a versão de homolog

```
$ ces-database-mirror
```


### Para corrigir permissões de arquivos e pastas

```
$ ces-permission-fix --AMBIENCE
```

### Para fazer o update do doctrine

```
$ ces-database-update
```

### Para limpar o cache:

```
$ ces-cache-clear
```

### Para compilar o frontend:

```
$ ces-frontend-compile
```

### Para start do sistema em desenvolvimento:

```
$ ces-server-start
```

### Para lint de bundles:

```
$ cd BUNDLE_PATH
$ yarn lint
```

Lista de Comandos
=================

Comandos utilizados no sistema Sices Solar.

### Para instalar todas as dependencias do Sices
```
$ ces-ambience-config
```

### Para realizar deploy
```
$ cd AMBIENCE
$ ces-app-deploy --AMBIENCE
```

### Para iniciar as aplicações
```
$ ces-app-start --AMBIENCE
```

### Para configurar a aplicação
```
$ ces-app-config --AMBIENCE
```

### Para instalar/atualizar todas as dependencias do composer e do yarn
```
$ ces-app-install
```

### Para acessar o ambiente de homolog
```
$ ssh -i $SICES_PATH/devops/aws/homolog.pem admin@54.233.150.10
```

### Para iniciar uma nova branch
```
$ ces-new-task ISSUE_NUMBER
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

### Para limpar o cache do Symfony
```
$ ces-cache-clear
```

### Para compilar o frontend
```
$ ces-frontend-compile
```

### Para lint de bundles
```
$ cd BUNDLE_PATH
$ npm run lint
```
